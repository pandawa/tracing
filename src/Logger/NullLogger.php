<?php

declare(strict_types=1);

namespace Pandawa\Tracing\Logger;

use Pandawa\Tracing\Contract\Logger;
use Pandawa\Tracing\Event;
use Pandawa\Tracing\TraceId;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class NullLogger implements Logger
{
    public function log(Event $event): ?TraceId
    {
        return null;
    }
}
