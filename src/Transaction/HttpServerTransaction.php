<?php

declare(strict_types=1);

namespace Pandawa\Tracing\Transaction;

use Illuminate\Http\Request;
use JsonException;
use Pandawa\Tracing\Util;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class HttpServerTransaction
{
    private array $data = [];

    public function start(Request $request): void
    {
        $this->data['op'] = 'http.server';
        $this->data['start_time'] = microtime(true);
        $this->data['requested_at'] = date('Y-m-d H:i:s');
        $this->data['request'] = [
            'path'         => '/'.ltrim($request->path(), '/'),
            'full_url'     => $request->fullUrl(),
            'method'       => strtoupper($request->method()),
            'headers'      => json_encode(Util::flattenHeaders($request->headers->all())),
            'query_params' => json_encode($this->filter($request->query->all())),
            'body'         => $this->filterBody($request->getContent()),
            'client_ip'    => $request->ip(),
            'user_agent'   => $request->userAgent(),
        ];
    }

    public function finish(Response $response): void
    {
        $this->data['finish_time'] = microtime(true);
        $this->data['response'] = [
            'headers'     => json_encode(Util::flattenHeaders($response->headers->all())),
            'status_code' => $response->getStatusCode(),
            'body'        => $this->getBody($response),
        ];
    }

    public function toArray(): array
    {
        return $this->data;
    }

    private function getBody(Response $response)
    {
        return $response->getContent();
    }

    private function filterBody(string $body): string
    {
        if ($this->isJson($body)) {
            $data = $this->filter(json_decode($body, true));

            return json_encode($data);
        }

        return $body;
    }

    private function filter(array $data): array
    {
        $filtered = [];
        $filters = config('tracing.filters', []);

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $value = $this->filter($value);
            } else if (is_string($key) && in_array($key, $filters)) {
                $value = '[FILTERED]';
            }

            $filtered[$key] = $value;
        }

        return $filtered;
    }

    private function isJson(string $value): bool
    {
        try {
            json_decode($value, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return false;
        }

        return true;
    }
}
