<?php

declare(strict_types=1);

namespace Jmf\RenderingPreset\Exception;

class InvalidPresetFileException extends InvalidConfigurationException
{
    public function __construct(string $path)
    {
        parent::__construct("Preset file '{$path}' must have a YAML mapping as its root.");
    }
}
