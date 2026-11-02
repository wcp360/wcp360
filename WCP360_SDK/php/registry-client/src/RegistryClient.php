<?php
declare(strict_types=1);
namespace Wcp360\Sdk\Registry;

/** Candidate vendor API consumer. Never imports Core, installs, downloads or executes a package. */
final class RegistryClient
{
    private string $base;
    private array $cache = [];

    public function __construct(string $baseUrl, private readonly Transport $transport, bool $acceptCandidate = false, bool $allowLoopbackHttp = false)
    {
        if (!$acceptCandidate) throw new ClientException('candidate_opt_in_required');
        UrlPolicy::validate($baseUrl, $allowLoopbackHttp);
        if (isset(parse_url($baseUrl)['query'])) throw new ClientException('invalid_endpoint');
        $this->base = rtrim($baseUrl, '/');
    }

    public function status(): array { return $this->request('/status', [], 'status'); }
    public function catalog(string $type, array $query = []): array
    {
        $path = ['plugin'=>'plugins','module'=>'modules','app'=>'apps'][$type] ?? null;
        if (!$path) throw new ClientException('invalid_type');
        return $this->request('/catalog/' . $path, $query, 'packages', ['page','per_page','q','pricing']);
    }
    public function package(string $type, string $slug): array
    {
        return $this->request($this->packagePath($type, $slug), [], 'package');
    }
    public function versions(string $type, string $slug, array $query = []): array
    {
        return $this->request($this->packagePath($type, $slug) . '/versions', $query, 'releases', ['page','per_page','channel']);
    }
    public function releases(array $query = []): array
    {
        return $this->request('/releases', $query, 'releases', ['page','per_page','channel']);
    }
    public function updates(array $environment): array
    {
        $fields = ['type','slug','current_version','channel','core_version','php_version','api_version','sdk_version','os','architecture'];
        foreach (array_diff($fields, ['channel']) as $required) if (!isset($environment[$required])) throw new ClientException('incomplete_environment');
        return $this->request('/updates', $environment, 'update', $fields);
    }

    private function packagePath(string $type, string $slug): string
    {
        if (!in_array($type, ['plugin','module','app','core'], true) || !preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/D', $slug) || strlen($slug) > 80) throw new ClientException('invalid_identity');
        return '/packages/' . $type . '/' . $slug;
    }

    private function request(string $path, array $query, string $shape, array $allowed = []): array
    {
        foreach ($query as $key => $value) {
            if (!in_array($key, $allowed, true) || (!is_string($value) && !is_int($value)) || strlen((string)$value) > 200 || preg_match('/[\x00-\x1f\x7f]/', (string)$value)) throw new ClientException('invalid_query');
        }
        ksort($query);
        $url = $this->base . $path . ($query ? '?' . http_build_query($query, '', '&', PHP_QUERY_RFC3986) : '');
        $cached = $this->cache[$url] ?? null;
        try { $response = $this->transport->get($url, $cached ? ['If-None-Match'=>$cached['etag']] : []); }
        catch (ClientException $e) { throw $e; }
        catch (\Throwable) { throw new ClientException('transport_failed'); }
        if ($response->status === 304) {
            if (!$cached) throw new ClientException('unexpected_not_modified', 304);
            return $cached['data'];
        }
        if ($response->status !== 200) throw new ClientException($response->status === 429 ? 'rate_limited' : 'http_error', $response->status);
        if (strlen($response->body) > CurlTransport::MAX_BODY) throw new ClientException('response_too_large');
        if (!preg_match('~^application/json(?:\s*;|$)~i', $response->header('Content-Type'))) throw new ClientException('invalid_content_type');
        try { $envelope = json_decode($response->body, true, 32, JSON_THROW_ON_ERROR); }
        catch (\JsonException) { throw new ClientException('invalid_json'); }
        $data = PublicSchema::validate($envelope, $shape);
        $etag = $response->header('ETag');
        if (preg_match('/^(?:W\/)?"[a-f0-9]{64}"$/D', $etag) && preg_match('/(?:^|,)\s*public(?:\s*,|$)/i', $response->header('Cache-Control')) && !preg_match('/\b(?:no-store|private)\b/i', $response->header('Cache-Control'))) {
            unset($this->cache[$url]);
            if (count($this->cache) >= 8) array_shift($this->cache);
            $this->cache[$url] = ['etag'=>$etag,'data'=>$data];
        } else { unset($this->cache[$url]); }
        return $data;
    }
}
