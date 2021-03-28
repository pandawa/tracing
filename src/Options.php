<?php

declare(strict_types=1);

namespace Pandawa\Tracing;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class Options
{
    private OptionsResolver $resolver;

    public function __construct(array $options = [])
    {
        $this->resolver = new OptionsResolver();

        $this->configureOptions($this->resolver);

        $this->resolver->resolve($options);
    }

    private function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'timeout'       => 5,
            'send_attempts' => 3,
        ]);
    }
}
