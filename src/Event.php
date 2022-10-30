<?php

declare(strict_types=1);

namespace Pandawa\Tracing;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class Event
{
    public function __construct(
        public readonly array $data,
        public readonly ?string $source = null,
        public readonly ?string $topic = null
    ) {
    }
}
