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
        $this->data['request'] = array_filter([
            'uri'     => (string)$request->getUri(),
            'method'  => strtoupper($request->getMethod()),
            'headers' => Util::flattenHeaders($request->getHeaders()),
            'body'    => $this->parseBody($request),
        ]);
    }

    public function finish($response): void
    {
        $this->data['finish_time'] = microtime(true);

        if ($response instanceof ResponseInterface) {
            $this->data['response'] = [
                'headers'     => Util::flattenHeaders($response->getHeaders()),
                'status_code' => $response->getStatusCode(),
                'body'        => $this->parseBody($response),
            ];

            return;
        }

        if (is_scalar($response) || is_array($response)) {
            $this->data['response'] = $response;
        }
    }

    public function toArray(): array
    {
        return $this->data;
    }

    private function parseBody(MessageInterface $message)
    {
        if ($contentType = $message->getHeader('Content-Type')) {
            if (false !== array_search('application/json', $contentType)) {
                return json_decode($message->getBody()->getContents(), true);
            }
        }

        return $message->getBody()->getContents();
    }
}
