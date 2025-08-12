<?php

declare(strict_types=1);

namespace Jmf\PresetRendering\Exception;

class PresetNotFoundException extends PresetRenderingException
{
    public function __construct(
        private readonly string $presetId,
    ) {
        parent::__construct("Preset '{$this->presetId}' not found.");
    }
}
