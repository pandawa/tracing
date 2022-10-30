<?php

declare(strict_types=1);

namespace Pandawa\Tracing;

use Pandawa\Tracing\Contract\LoggerInterface;
use Pandawa\Tracing\Contract\TracerInterface;
use Pandawa\Tracing\Job\CaptureEvent;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class Tracer implements TracerInterface
{
    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    public function capture(Event $event): void
    {
        if ($queue = config('tracing.capture_in_queue')) {
            $this->captureInQueue($event, $queue, config('tracing.queue_connection'));

            return;
        }

        $this->captureNow($event);
    }

    private function captureNow(Event $event): void
    {
        $this->logger->log($event);
    }

    private function captureInQueue(Event $event, $queue, $connection): void
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
}
