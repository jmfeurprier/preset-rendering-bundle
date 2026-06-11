<?php

declare(strict_types=1);

namespace Jmf\RenderingPreset\Preset\Property;

use Jmf\RenderingPreset\Exception\MissingRequiredPropertyValueException;
use Jmf\RenderingPreset\Exception\PropertyValueDomainException;
use Jmf\RenderingPreset\Property\Property;

readonly class PresetPropertyLoader
{
    /**
     * @param array<string, mixed> $presetConfig
     *
     * @throws MissingRequiredPropertyValueException
     * @throws PropertyValueDomainException
     */
    public function load(
        string $presetId,
        array $presetConfig,
        Property $property,
    ): PresetProperty {
        return new PresetProperty(
            $property->getKey(),
            $this->getValue($presetId, $presetConfig, $property),
        );
    }

    /**
     * @param array<string, mixed> $presetConfig
     *
     * @throws MissingRequiredPropertyValueException
     * @throws PropertyValueDomainException
     */
    private function getValue(
        string $presetId,
        array $presetConfig,
        Property $property,
    ): mixed {
        $key = $property->getKey();

        if (array_key_exists($key, $presetConfig)) {
            $value = $presetConfig[$key];

            $this->validateValue($presetId, $property, $value);

            return $value;
        }

        if ($property->isRequired()) {
            throw new MissingRequiredPropertyValueException($property, $presetId);
        }

        return $property->getDefault();
    }

    /**
     * @throws PropertyValueDomainException
     */
    private function validateValue(
        string $presetId,
        Property $property,
        mixed $value,
    ): void {
        $choices = $property->getChoices();

        if ([] === $choices) {
            return;
        }

        if (!in_array($value, (array) $choices, true)) {
            throw new PropertyValueDomainException($property, $presetId);
        }
    }
}
