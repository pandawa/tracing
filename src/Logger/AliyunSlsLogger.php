<?php

declare(strict_types=1);

namespace Pandawa\Tracing\Logger;

use Aliyun\SLS\Client;
use Aliyun\SLS\Models\LogItem;
use Aliyun\SLS\Requests\PutLogsRequest;
use Illuminate\Support\Arr;
use Pandawa\Tracing\Contract\LoggerInterface;
use Pandawa\Tracing\Event;
use Pandawa\Tracing\TraceId;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class AliyunSlsLogger implements LoggerInterface
{
    private array $options;
    private Client $client;

    public function __construct(array $options)
    {
        $resolver = new OptionsResolver();

        $this->configureDefaults($resolver);

        $this->options = $resolver->resolve($options);
        $this->client = new Client(
            $this->options['endpoint'],
            $this->options['access_key_id'],
            $this->options['access_key_secret']
        );
    }

    public function log(Event $event): ?TraceId
    {
        $traceId = TraceId::generate();
        $request = new PutLogsRequest(
            $this->options['project'],
            $this->options['log_store'],
            $event->topic ?? $this->options['topic'],
            $event->source ?? $this->options['source'],
            [
                new LogItem(array_merge(
                    array_filter(Arr::dot($event->data)),
                    ['_trace_id' => (string)$traceId]
                )),
            ]
        );

        try {
            $this->client->putLogs($request);
        } catch (\Exception $e) {
            if (0 !== $e->getCode()) {
                throw $e;
            }
        }

        return $traceId;
    }

    private function configureDefaults(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'access_key_id'     => null,
            'access_key_secret' => null,
            'endpoint'          => 'ap-southeast-5.log.aliyuncs.com',
            'project'           => null,
            'log_store'         => null,
            'topic'             => null,
            'source'            => null,
        ]);
    }
}
