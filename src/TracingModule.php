<?php

declare(strict_types=1);

namespace Pandawa\Tracing;

use Illuminate\Contracts\Http\Kernel as HttpKernelInterface;
use Pandawa\Component\Module\AbstractModule;
use Pandawa\Pavana\Contract\HttpClient;
use Pandawa\Tracing\Contract\Logger;
use Pandawa\Tracing\Contract\Tracer as TracerContract;
use Pandawa\Tracing\Middleware\Middleware;
use Pandawa\Tracing\Plugin\PavanaTracePlugin;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class TracingModule extends AbstractModule
{
    public function provides(): array
    {
        return [
            LogManager::class,
            Logger::class,
            TracerContract::class,
        ];
    }

    protected function build(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/tracing.php' => config_path('tracing.php'),
            ], 'tracing');
        }

        $httpKernel = $this->app->make(HttpKernelInterface::class);

        $httpKernel->prependMiddleware(Middleware::class);

        foreach ((array) $this->app['config']['tracing.pavana'] as $key => $value) {
            $service = is_int($key) ? $value : $key;
            $options = is_int($key) ? [] : $value;

            $this->addPavanaPlugin($service, $options);
        }
    }

    protected function init(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/tracing.php', 'tracing');

        $this->app->singleton(LogManager::class);
        $this->app->alias(LogManager::class, Logger::class);

        $this->app->singleton(TracerContract::class, Tracer::class);
        $this->app->singleton(Middleware::class);
    }

    private function addPavanaPlugin(string $service, array $options = [])
    {
        $service = $this->app[$service];

        if ($service instanceof HttpClient) {
            $service->addPlugin(
                new PavanaTracePlugin(
                    $this->app[TracerContract::class],
                    $options['topic'] ?? null,
                )
            );
        }
    }
}
