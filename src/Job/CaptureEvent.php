<?php

declare(strict_types=1);

namespace Pandawa\Tracing\Job;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Pandawa\Tracing\Contract\Logger;
use Pandawa\Tracing\Event;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class CaptureEvent implements ShouldQueue
{
    use Queueable;

    private Event $event;

    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    public function handle(Logger $logger): void
    {
        $logger->log($this->event);
    }
}
