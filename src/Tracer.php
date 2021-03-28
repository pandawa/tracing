<?php

declare(strict_types=1);

namespace Pandawa\Tracing;

use Pandawa\Tracing\Contract\Logger;
use Pandawa\Tracing\Contract\Tracer as TracerContract;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class Tracer implements TracerContract
{
    private Logger $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function capture(Event $event): void
    {
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
        $job = dispatch(function () use ($event) {
            $this->logger->log($event);
        });

        if (is_string($queue)) {
            $job->onQueue($queue);
        }

        if ($connection) {
            $job->onConnection($connection);
        }
    }
}
