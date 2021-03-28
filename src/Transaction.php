<?php

declare(strict_types=1);

namespace Pandawa\Tracing;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
        $this->data['request'] = [
            'path'         => '/'.ltrim($request->path(), '/'),
            'full_url'     => $request->fullUrl(),
            'method'       => strtoupper($request->method()),
            'headers'      => $request->headers->all(),
            'query_params' => $request->query->all(),
            'params'       => $request->isJson() ? $request->json()->all() : $request->request->all(),
            'client_ip'    => $request->ip(),
            'user_agent'   => $request->userAgent(),
        ];
    }

    public function finish(Response $response): void
    {
        $this->data['finish_time'] = microtime(true);
        $this->data['response'] = [
            'headers'     => $response->headers->all(),
            'status_code' => $response->getStatusCode(),
            'body'        => $response->content(),
        ];
    }

    public function toArray(): array
    {
        return $this->data;
    }
}
