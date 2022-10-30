<?php

declare(strict_types=1);

namespace Pandawa\Tracing\Contract;

use Pandawa\Tracing\Event;
use Pandawa\Tracing\TraceId;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface LoggerInterface
{
    public function log(Event $event): ?TraceId;
}
