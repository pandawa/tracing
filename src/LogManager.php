<?php

declare(strict_types=1);

namespace Pandawa\Tracing;

use Illuminate\Support\Manager;
use Pandawa\Tracing\Contract\Logger;
use Pandawa\Tracing\Logger\AliyunSlsLogger;
use Pandawa\Tracing\Logger\NullLogger;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class LogManager extends Manager implements Logger
{
    public function createNullDriver(): NullLogger
    {
        return new NullLogger();
    }

    public function createAliyunDriver(): AliyunSlsLogger
    {
        return new AliyunSlsLogger($this->config->get('tracing.loggers.aliyun'));
    }

    public function log(Event $event): ?TraceId
    {
        return $this->driver()->log($event);
    }

    public function getDefaultDriver(): string
    {
        return $this->config->get('tracing.default', 'null');
    }
}
