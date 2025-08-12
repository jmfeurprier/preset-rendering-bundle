<?php

declare(strict_types=1);

namespace Jmf\PresetRendering\Exception;

use Jmf\PresetRendering\Preset\Preset;
use Throwable;

class UnreadableObjectPropertyException extends PresetRenderingException
{
    public function __construct(
        private readonly Preset $preset,
        private readonly object $object,
        private readonly string $property,
        ?Throwable $previous = null,
    ) {
        $class = $this->object::class;

        parent::__construct(
            message:  "Cannot read property {$this->property} from {$class} object in preset '{$this->preset->getId()}'.",
            previous: $previous,
        );
    }

    public function getPreset(): Preset
    {
        return $this->preset;
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
