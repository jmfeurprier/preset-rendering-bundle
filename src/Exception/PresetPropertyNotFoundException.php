<?php

declare(strict_types=1);

namespace Jmf\RenderingPreset\Exception;

class PresetPropertyNotFoundException extends RenderingPresetException
{
    public function __construct(
        private readonly string $propertyKey,
    ) {
        parent::__construct("Preset Property '{$this->propertyKey}' not found.");
    }
}
