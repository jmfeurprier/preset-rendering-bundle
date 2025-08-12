<?php

declare(strict_types=1);

namespace Jmf\PresetRendering\Exception;

class PresetPropertyNotFoundException extends PresetRenderingException
{
    public function __construct(
        private readonly string $propertyKey,
    ) {
        parent::__construct("Preset Property '{$this->propertyKey}' not found.");
    }
}
