<?php

declare(strict_types=1);

namespace Pandawa\Tracing\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Pandawa\Tracing\Contract\Tracer;
use Pandawa\Tracing\Event;
use Pandawa\Tracing\Transaction;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class Middleware
{
    private ?Transaction $transaction = null;

    public function handle(Request $request, Closure $next)
    {
        if (app()->bound(Tracer::class)) {
            $this->transaction = new Transaction();
            $this->transaction->start($request);
        }

        return $next($request);
    }

    public function terminate($request, $response): void
    {
        if ($this->transaction) {
            $this->transaction->finish($response);

            app(Tracer::class)->capture(new Event(
                array_merge($this->transaction->toArray(), ['__tag__:__hostname__' => $this->getHostName()]),
                $this->getServerIp()
            ));
        }
    }

    private function getHostName(): string
    {
        return gethostname();
    }

    private function getServerIp(): string
    {
        return gethostbyname($this->getHostName());
    }
}
