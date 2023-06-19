<?php

declare(strict_types=1);

namespace Pandawa\Tracing;

use Pandawa\Tracing\Contract\Logger;
use Pandawa\Tracing\Contract\Tracer as TracerContract;
use Pandawa\Tracing\Job\CaptureEvent;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class Tracer implements TracerContract
{
    private Logger $logger;
    private array $filters = [];

    public function __construct(Logger $logger, array $filters = [])
    {
        $this->logger = $logger;
        $this->filters = $filters;
    }

    public function capture(Event $event): void
    {
        if (!empty($this->filters) && count($event->getData())) {
            $event = new Event(
                $this->filter($event->getData()),
                $event->getSource(),
                $event->getTopic(),
            );
        }

        if ($queue = config('tracing.capture_in_queue')) {
            $this->captureLater($event, $queue, config('tracing.queue_connection'));

            return;
        }

        $this->captureNow($event);
    }

    private function captureNow(Event $event): void
    {
        $this->logger->log($event);
    }

    private function captureLater(Event $event, $queue, $connection): void
    {
        $job = new CaptureEvent($event);

        if (is_string($queue)) {
            $job->onQueue($queue);
        }

        if ($connection) {
            $job->onConnection($connection);
        }

        dispatch($job);
    }

    private function filter(array $data): array
    {
        $filtered = [];

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $value = $this->filter($value);
            } else if (is_string($key) && in_array($key, $this->filters)) {
                $value = '[FILTERED]';
            }

            $filtered[$key] = $value;
        }

        return $filtered;
    }
}
