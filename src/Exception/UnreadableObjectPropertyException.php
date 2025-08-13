<?php

declare(strict_types=1);

namespace Jmf\RenderingPreset\Exception;

use Jmf\RenderingPreset\Preset\Preset;
use Throwable;

class UnreadableObjectPropertyException extends UnreadableItemValueException
{
    public function __construct(
        Preset $preset,
        private readonly object $object,
        private readonly string $property,
        ?Throwable $previous = null,
    ) {
        parent::__construct(
            preset:   $preset,
            message:  sprintf(
                          "Cannot read property %s from %s object in preset '%s'.",
                          $this->property,
                          $this->object::class,
                          $this->getPreset()->getId(),
                      ),
            previous: $previous,
        );
    }

    public function getObject(): object
    {
        return $this->object;
    }

    public function getProperty(): string
    {
        return $this->property;
    }
}
