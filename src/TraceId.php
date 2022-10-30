<?php

declare(strict_types=1);

namespace Pandawa\Tracing;

use Illuminate\Support\Str;
use InvalidArgumentException;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class TraceId
{
    private readonly string $value;

    public function __construct(string $value)
    {
        if (!preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $value)) {
            throw new InvalidArgumentException('The $value should be uuid.');
        }

        $this->value = $value;
    }

    public static function generate(): self
    {
        return new self((string) Str::uuid());
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
