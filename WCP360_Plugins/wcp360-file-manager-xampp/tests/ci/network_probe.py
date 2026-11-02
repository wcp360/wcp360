"""Allowlisted structured network probes; no responses, URLs or headers exported."""
import socket
from urllib.request import build_opener, ProxyHandler, Request
from urllib.error import HTTPError


def direct_denied():
    for address in [('1.1.1.1',443),('2606:4700:4700::1111',443)]:
        try:
            with socket.create_connection(address,timeout=2):pass
        except OSError:pass
        else:raise RuntimeError('DIRECT_EGRESS_OPEN')
    with socket.socket(socket.AF_INET,socket.SOCK_DGRAM) as s:
        s.settimeout(2)
        # DNS query for example.com A, sent to an external resolver (no system resolver fallback).
        packet=b'\x12\x34\x01\x00\x00\x01\x00\x00\x00\x00\x00\x00\x07example\x03com\x00\x00\x01\x00\x01'
        try:s.sendto(packet,('8.8.8.8',53));s.recvfrom(512)
        except OSError:pass
        else:raise RuntimeError('DIRECT_DNS_OPEN')
    return {'external_tcp_blocked':True,'external_ipv4_tcp_blocked':True,'external_ipv6_tcp_blocked':True,'external_dns_blocked':True}


def proxy_probe(proxy):
    opener=build_opener(ProxyHandler({'http':proxy,'https':proxy}))
    with opener.open(Request('https://repo.packagist.org/packages.json',method='HEAD'),timeout=20) as r:
        if r.status!=200:raise RuntimeError('PROXY_CONTROL_UNAVAILABLE')
    # Send CONNECT directly so denial is distinguished from arbitrary remote/network failure.
    from urllib.parse import urlsplit
    parsed=urlsplit(proxy)
    with socket.create_connection((parsed.hostname,parsed.port),timeout=5) as s:
        s.sendall(b'CONNECT example.com:443 HTTP/1.1\r\nHost: example.com:443\r\n\r\n')
        response=s.recv(256)
        if not response.startswith(b'HTTP/1.1 403 '):raise RuntimeError('PROXY_DENY_NOT_PROVEN')
    return {'allowlisted_https_reachable':True,'unlisted_host_rejected':True,**direct_denied()}
