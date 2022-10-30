<?php

declare(strict_types=1);

namespace Pandawa\Tracing\Job;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Pandawa\Tracing\Contract\LoggerInterface;
use Pandawa\Tracing\Event;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class CaptureEvent implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Event $event)
    {
    }

    public function __invoke(LoggerInterface $logger): void
    {
        $logger->log($this->event);
    }
}
