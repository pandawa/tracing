<?php

declare(strict_types=1);

namespace Pandawa\Tracing\Logger;

use Aliyun\SLS\Client;
use Aliyun\SLS\Models\LogItem;
use Aliyun\SLS\Requests\PutLogsRequest;
use Illuminate\Support\Arr;
use Pandawa\Tracing\Contract\Logger;
use Pandawa\Tracing\Event;
use Pandawa\Tracing\TraceId;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class AliyunSlsLogger implements Logger
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
            $event->getTopic() ?? $this->options['topic'],
            $event->getSource() ?? $this->options['source'],
            [
                new LogItem(array_merge(
                    Arr::dot($event->getData()),
                    ['_trace_id' => (string)$traceId]
                )),
            ]
        );

        $this->client->putLogs($request);

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
