<?php

declare(strict_types=1);

namespace Jmf\RenderingPreset\Exception;

class PresetNotFoundException extends RenderingPresetException
{
    public function __construct(
        private readonly string $presetId,
    ) {
        parent::__construct("Preset '{$this->presetId}' not found.");
    }
}
