<?php

declare(strict_types=1);

namespace Pandawa\Tracing;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class Event
{
    private array $data;
    private ?string $source;
    private ?string $topic;

    public function __construct(array $data, string $source = null, string $topic = null)
    {
        $this->data = $data;
        $this->source = $source;
        $this->topic = $topic;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function getTopic(): ?string
    {
        return $this->topic;
    }
}
