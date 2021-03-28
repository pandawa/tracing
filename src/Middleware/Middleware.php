<?php

declare(strict_types=1);

namespace Pandawa\Tracing\Middleware;

use Closure;
use Illuminate\Http\Request;
use Pandawa\Tracing\Contract\Tracer;
use Pandawa\Tracing\Event;
use Pandawa\Tracing\Transaction\HttpServerTransaction;
use Pandawa\Tracing\Util;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class Middleware
{
    private ?HttpServerTransaction $transaction = null;

    public function handle(Request $request, Closure $next)
    {
        if (app()->bound(Tracer::class)) {
            $this->transaction = new HttpServerTransaction();
            $this->transaction->start($request);
        }

        return $next($request);
    }

    public function terminate($request, $response): void
    {
        if ($this->transaction) {
            $this->transaction->finish($response);

            app(Tracer::class)->capture(new Event(
                array_merge(
                    $this->transaction->toArray(),
                    ['__tag__:__hostname__' => Util::getHostname()]
                ),
                Util::getServerIp()
            ));
        }
    }
}
