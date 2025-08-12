<?php

declare(strict_types=1);

namespace Jmf\PresetRendering\Preset\Property;

use Jmf\PresetRendering\Exception\PresetRenderingException;
use Jmf\PresetRendering\Property\Property;

readonly class PresetPropertyLoader
{
    /**
     * @param array<string, mixed> $presetConfig
     *
     * @throws PresetRenderingException
     */
    public function load(
        array $presetConfig,
        Property $property,
    ): PresetProperty {
        return new PresetProperty(
            $property->getKey(),
            $this->getValue($presetConfig, $property),
        );
    }

    /**
     * @param array<string, mixed> $presetConfig
     *
     * @throws PresetRenderingException
     */
    private function getValue(
        array $presetConfig,
        Property $property,
    ): mixed {
        $key = $property->getKey();

        if (array_key_exists($key, $presetConfig)) {
            $value = $presetConfig[$key];

            $this->validateValue($property, $value);

            return $value;
        }

        if ($property->isRequired()) {
            // @todo
            throw new PresetRenderingException('Preset property value is required.');
        }

        return $property->getDefault();
    }

    /**
     * @throws PresetRenderingException
     */
    private function validateValue(
        Property $property,
        mixed $value,
    ): void {
        $choices = $property->getChoices();

        if ([] === $choices) {
            return;
        }

        if (!in_array($value, (array) $choices, true)) {
            // @todo
            throw new PresetRenderingException('Preset property value is not part of available choices.');
        }
    }
}
