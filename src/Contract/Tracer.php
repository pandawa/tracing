<?php

declare(strict_types=1);

namespace Pandawa\Tracing\Contract;

use Pandawa\Tracing\Event;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface Tracer
{
    public function capture(Event $event): void;
}
