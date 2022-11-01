<?php

declare(strict_types=1);

namespace Pandawa\Tracing;

use Illuminate\Contracts\Http\Kernel as HttpKernelInterface;
use Pandawa\Bundle\DependencyInjectionBundle\Plugin\ImportServicesPlugin;
use Pandawa\Bundle\FoundationBundle\Plugin\ImportConfigurationPlugin;
use Pandawa\Component\Foundation\Bundle\Bundle;
use Pandawa\Component\Foundation\Http\Kernel;
use Pandawa\Contracts\Foundation\HasPluginInterface;
use Pandawa\Pavana\HttpClient\HttpClient;
use Pandawa\Tracing\Contract\TracerInterface;
use Pandawa\Tracing\Plugin\PavanaTracePlugin;
use RuntimeException;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class TracingBundle extends Bundle implements HasPluginInterface
{
    public function boot(): void
    {
        $this->traceIncomingHttp();
        $this->traceOutgoingHttp();
    }

    public function plugins(): array
    {
        return [
            new ImportConfigurationPlugin(),
            new ImportServicesPlugin(),
        ];
    }

    protected function traceIncomingHttp(): void
    {
        /** @var Kernel $httpKernel */
        $httpKernel = $this->app[HttpKernelInterface::class];
        $httpKernel->prependMiddleware('tracing.middleware.http_server');
    }

    protected function traceOutgoingHttp(): void
    {
        $pavanaClients = $this->app['config']['tracing.pavana'] ?? [];

        if (count($pavanaClients) && !class_exists(HttpClient::class)) {
            throw new RuntimeException(
                'Pavana component was not installed. Please run "composer install pandawa/pavana".'
            );
        }

        foreach ($pavanaClients as $key => $value) {
            $service = is_int($key) ? $value : $key;
            $options = is_int($key) ? [] : $value;

            $this->addPavanaPlugin($service, $options);
        }
    }

    protected function addPavanaPlugin(string $service, array $options = [])
    {
        $service = $this->app[$service];

        if ($service instanceof HttpClient) {
            $service->prependPlugin(
                new PavanaTracePlugin(
                    $this->app[TracerInterface::class],
                    $options['topic'] ?? null,
                )
            );
        }
    }
}
