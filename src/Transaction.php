<?php

declare(strict_types=1);

namespace Pandawa\Tracing;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class Transaction
{
    private array $data = [];

    public function start(Request $request): void
    {
        $this->data['op'] = 'http.server';
        $this->data['start_time'] = microtime(true);
        $this->data['requested_at'] = date('Y-m-d H:i:s');
        $this->data['request'] = array_filter([
            'path'         => '/'.ltrim($request->path(), '/'),
            'full_url'     => $request->fullUrl(),
            'method'       => strtoupper($request->method()),
            'headers'      => $this->headers($request->headers->all()),
            'query_params' => $request->query->all(),
            'params'       => $request->isJson() ? $request->json()->all() : $request->request->all(),
            'client_ip'    => $request->ip(),
            'user_agent'   => $request->userAgent(),
        ]);
    }

    public function finish(Response $response): void
    {
        $this->data['finish_time'] = microtime(true);
        $this->data['response'] = array_filter([
            'headers'     => $this->headers($response->headers->all()),
            'status_code' => $response->getStatusCode(),
            'body'        => $this->getBody($response),
        ]);
    }

    public function toArray(): array
    {
        return $this->data;
    }

    private function headers(array $headers): array
    {
        return array_map(function ($headers) {
            if (is_array($headers)) {
                return implode(';', $headers);
            }

            return $headers;
        }, $headers);
    }

    private function getBody(Response $response)
    {
        if ($response instanceof JsonResponse) {
            return $response->getData(true);
        }

        return $response->getContent();
    }
}
