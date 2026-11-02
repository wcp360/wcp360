<?php
declare(strict_types=1);
namespace Wcp360\Sdk\Registry;

final class CurlTransport implements Transport
{
    public const MAX_BODY = 2097152;
    public function __construct(private readonly bool $allowLoopbackHttp = false) {}

    public function get(string $url, array $headers = []): TransportResponse
    {
        UrlPolicy::validate($url, $this->allowLoopbackHttp);
        if (!extension_loaded('curl')) throw new ClientException('curl_required');
        $lines = ['Accept: application/json'];
        foreach ($headers as $key => $value) {
            if ($key !== 'If-None-Match' || !is_string($value) || !preg_match('/^(?:W\/)?"[a-f0-9]{64}"$/D', $value)) throw new ClientException('invalid_header');
            $lines[] = $key . ': ' . $value;
        }
        $body = '';
        $received = [];
        $headerBytes = 0;
        $overflow = false;
        $curl = curl_init($url);
        if ($curl === false) throw new ClientException('transport_unavailable');
        try {
            curl_setopt_array($curl, [
                CURLOPT_FOLLOWLOCATION=>false, CURLOPT_MAXREDIRS=>0,
                CURLOPT_CONNECTTIMEOUT=>3, CURLOPT_TIMEOUT=>10, CURLOPT_NOSIGNAL=>true,
                CURLOPT_SSL_VERIFYPEER=>true, CURLOPT_SSL_VERIFYHOST=>2,
                CURLOPT_PROTOCOLS=>CURLPROTO_HTTPS | ($this->allowLoopbackHttp ? CURLPROTO_HTTP : 0),
                CURLOPT_HTTPHEADER=>$lines, CURLOPT_USERAGENT=>'WCP360-Registry-Candidate/0.1.0',
                CURLOPT_WRITEFUNCTION=>static function ($handle, string $chunk) use (&$body, &$overflow): int {
                    if (strlen($body) + strlen($chunk) > self::MAX_BODY) { $overflow = true; return 0; }
                    $body .= $chunk;
                    return strlen($chunk);
                },
                CURLOPT_HEADERFUNCTION=>static function ($handle, string $line) use (&$received, &$headerBytes, &$overflow): int {
                    $headerBytes += strlen($line);
                    if ($headerBytes > 32768) { $overflow = true; return 0; }
                    if (str_starts_with($line, 'HTTP/')) $received = [];
                    elseif (str_contains($line, ':')) {
                        [$key,$value] = explode(':', $line, 2);
                        $received[strtolower(trim($key))] = trim($value);
                    }
                    return strlen($line);
                },
            ]);
            $ok = curl_exec($curl);
            if ($overflow) throw new ClientException('response_too_large');
            if ($ok === false) throw new ClientException('transport_failed');
            $status = (int)curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
            if ($status >= 300 && $status < 400 && $status !== 304) throw new ClientException('redirect_rejected', $status);
            return new TransportResponse($status, $received, $body);
        } finally { curl_close($curl); }
    }
}
