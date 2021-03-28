<?php

declare(strict_types=1);

namespace Pandawa\Tracing\Test\Unit;

use Pandawa\Tracing\Event;
use Pandawa\Tracing\Logger\AliyunSlsLogger;
use Pandawa\Tracing\Test\TestCase;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class AliyunSlsTest extends TestCase
{
    public function testPutLog(): void
    {
        $tracer = new AliyunSlsLogger([
            'access_key_id'     => 'ACCESS_KEY_ID',
            'access_key_secret' => 'ACCESS_KEY_SECRET',
            'project'           => 'ammana-dev',
            'log_store'         => 'ammana-dev-logstore',
            'source'            => '127.0.0.1',
            'topic'             => 'pandawa',
        ]);

        $traceId = $tracer->log(new Event(
            ['user' => ['name' => 'Iqbal', 'id' => 1], 'message' => 'I love the world!'],
            '127.0.0.1',
            'pandawa'
        ));

        $this->assertNotNull($traceId);
    }
}
