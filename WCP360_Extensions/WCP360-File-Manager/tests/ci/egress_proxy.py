"""Minimal CONNECT-only proxy: exact hosts, public destinations, no raw request logs."""
import ipaddress
import json
import os
from pathlib import Path
import select
import signal
import socket
import socketserver
import sys
import threading
import time

POLICY={}
COUNTS={'acquisition':{},'audit':{},'denied':0}
LOCK=threading.Lock()


def allowed_target(authority, hosts):
    parts=authority.split(':')
    if len(parts)!=2 or parts[1]!='443' or parts[0] not in hosts:
        raise ValueError('DESTINATION_DENIED')
    return parts[0]


class Proxy(socketserver.StreamRequestHandler):
    def handle(self):
        self.connection.settimeout(15)
        phase=self.server.phase
        try:
            line=self.rfile.readline(8193)
            if len(line)>8192: raise ValueError('REQUEST_LIMIT')
            method,authority,version=line.decode('ascii').strip().split(' ')
            if method!='CONNECT' or version not in ('HTTP/1.0','HTTP/1.1'):raise ValueError('METHOD')
            host=allowed_target(authority,POLICY[phase])
            total=0
            while True:
                line=self.rfile.readline(8193);total+=len(line)
                if total>16384 or not line:raise ValueError('HEADERS')
                if line in (b'\r\n',b'\n'):break
            addresses=socket.getaddrinfo(host,443,type=socket.SOCK_STREAM)
            if not addresses or any(not ipaddress.ip_address(a[4][0]).is_global for a in addresses):raise ValueError('ADDRESS_DENIED')
            # Pin the resolved address for this connection; do not resolve again after validation.
            upstream=None
            for family,kind,proto,_,address in addresses:
                try:
                    upstream=socket.socket(family,kind,proto);upstream.settimeout(15);upstream.connect(address);break
                except OSError:
                    if upstream:upstream.close()
                    upstream=None
            if upstream is None:raise OSError('UPSTREAM_UNAVAILABLE')
            with upstream:
                with LOCK: COUNTS[phase][host]=COUNTS[phase].get(host,0)+1
                self.wfile.write(b'HTTP/1.1 200 Connection established\r\n\r\n');self.wfile.flush()
                deadline=time.monotonic()+600
                while time.monotonic()<deadline:
                    ready,_,_=select.select([self.connection,upstream],[],[],30)
                    if not ready:return
                    for stream in ready:
                        data=stream.recv(65536)
                        if not data:return
                        (upstream if stream is self.connection else self.connection).sendall(data)
        except Exception:
            with LOCK:COUNTS['denied']+=1
            try:self.wfile.write(b'HTTP/1.1 403 Forbidden\r\nContent-Length: 0\r\nConnection: close\r\n\r\n')
            except OSError:pass


class Server(socketserver.ThreadingTCPServer):
    allow_reuse_address=True
    daemon_threads=True
    def handle_error(self,*args):pass


def stop(*args):
    # Hostnames come only from the checked-in allowlist; no URLs, headers or credentials.
    Path(sys.argv[2]).write_text(json.dumps(COUNTS,sort_keys=True)+'\n')
    os._exit(0)


if __name__=='__main__':
    POLICY=json.loads(Path(sys.argv[1]).read_text())
    signal.signal(signal.SIGTERM,stop)
    for phase,port in [('acquisition',18080),('audit',18081)]:
        server=Server(('0.0.0.0',port),Proxy);server.phase=phase
        threading.Thread(target=server.serve_forever,daemon=True).start()
    while True:time.sleep(30)
