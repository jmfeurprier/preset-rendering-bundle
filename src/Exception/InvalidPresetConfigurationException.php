<?php

declare(strict_types=1);

namespace Jmf\RenderingPreset\Exception;

class InvalidPresetConfigurationException extends InvalidConfigurationException
{
    public function __construct(string $presetId)
    {
        parent::__construct(
            sprintf(
                "Preset '%s' configuration must be a YAML mapping.",
                $presetId,
            ),
        );
    }
}
