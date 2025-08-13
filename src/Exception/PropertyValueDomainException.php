<?php

declare(strict_types=1);

namespace Jmf\RenderingPreset\Exception;

use Jmf\RenderingPreset\Property\Property;

class PropertyValueDomainException extends InvalidConfigurationException
{
    public function __construct(
        private readonly Property $property,
        private readonly string $presetId,
    ) {
        parent::__construct(
            message: sprintf(
                         "Preset property value is not part of available choices (property: '%s', preset: '%s').",
                         $this->property->getKey(),
                         $this->presetId,
                     ),
        );
    }

    public function getProperty(): Property
    {
        return $this->property;
    }

    public function getPresetId(): string
    {
        return $this->presetId;
    }
}
