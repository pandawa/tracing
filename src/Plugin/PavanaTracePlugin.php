<?php

declare(strict_types=1);

namespace Pandawa\Tracing\Plugin;

use Http\Client\Common\Plugin;
use Http\Promise\Promise;
use Pandawa\Tracing\Contract\Tracer;
use Pandawa\Tracing\Event;
use Pandawa\Tracing\Transaction\HttpClientTransaction;
use Pandawa\Tracing\Util;
use Psr\Http\Message\RequestInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class PavanaTracePlugin implements Plugin
{
    private Tracer $tracer;
    private ?string $topic;

    public function __construct(Tracer $tracer, ?string $topic)
    {
        $this->tracer = $tracer;
        $this->topic = $topic;
    }

    public function handleRequest(RequestInterface $request, callable $next, callable $first): Promise
    {
        $transaction = new HttpClientTransaction();
        $promise = $next($request);

        return $promise->then(function ($response) use ($request, $transaction) {
            $transaction->start($request);
            $transaction->finish($response);

            $this->tracer->capture(new Event(
                array_merge(
                    $transaction->toArray(),
                    ['__tag__:__hostname__' => Util::getHostname()]
                ),
                Util::getServerIp(),
                $this->topic
            ));
        });
    }
}
