<?php

declare(strict_types=1);

namespace Jmf\RenderingPreset\Preset\Property;

use Jmf\RenderingPreset\Exception\MissingRequiredPropertyValueException;
use Jmf\RenderingPreset\Exception\PropertyValueDomainException;
use Jmf\RenderingPreset\Property\PropertyCollection;

readonly class PresetPropertyCollectionLoader
{
    public function __construct(
        private PresetPropertyLoader $presetPropertyLoader,
    ) {
    }

    /**
     * @param array<string, mixed> $presetConfig
     *
     * @throws MissingRequiredPropertyValueException
     * @throws PropertyValueDomainException
     */
    public function load(
        string $presetId,
        array $presetConfig,
        PropertyCollection $propertyCollection,
    ): PresetPropertyCollection {
        $presetProperties = [];

        foreach ($propertyCollection->all() as $property) {
            $presetProperties[] = $this->presetPropertyLoader->load(
                $presetId,
                $presetConfig,
                $property,
            );
        }

        return new PresetPropertyCollection($presetProperties);
    }
}
