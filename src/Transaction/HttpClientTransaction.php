<?php

declare(strict_types=1);

namespace Pandawa\Tracing\Transaction;

use Pandawa\Tracing\Util;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class HttpClientTransaction
{
    private array $data = [];

    public function __construct()
    {
        $this->data['start_time'] = microtime(true);
    }

    public function start(RequestInterface $request): void
    {
        $this->data['op'] = 'http.client';
        $this->data['requested_at'] = date('Y-m-d H:i:s');
        $this->data['request'] = [
            'uri'     => (string)$request->getUri(),
            'method'  => strtoupper($request->getMethod()),
            'headers' => json_encode(Util::flattenHeaders($request->getHeaders())),
            'body'    => $this->parseBody($request, array_first($request->getHeader('Content-Type') ?? [])),
        ];
    }

    public function finish($response): void
    {
        $this->data['finish_time'] = microtime(true);

        if ($response instanceof ResponseInterface) {
            $this->data['response'] = [
                'headers'     => json_encode(Util::flattenHeaders($response->getHeaders())),
                'status_code' => $response->getStatusCode(),
                'body'        => $this->parseBody($response, array_first($response->getHeader('Content-Type') ?? [])),
            ];

            return;
        }

        if (is_scalar($response) || is_array($response)) {
            $this->data['response'] = is_array($response) ? json_encode($response) : $response;
        }
    }

    public function toArray(): array
    {
        return $this->data;
    }

    private function parseBody(MessageInterface $message, ?string $contentType = null): string
    {
        $message->getBody()->seek(0);
        $body = (string) $message->getBody();
        $message->getBody()->seek(0);

        return Util::normalizeContent($body, $contentType);
    }
}
