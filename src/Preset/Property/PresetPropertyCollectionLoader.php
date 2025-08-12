<?php

declare(strict_types=1);

namespace Jmf\PresetRendering\Preset\Property;

use Jmf\PresetRendering\Exception\NonUniquePropertyKeyException;
use Jmf\PresetRendering\Exception\PresetRenderingException;
use Jmf\PresetRendering\Property\Property;
use Jmf\PresetRendering\Property\PropertyCollection;
use Webmozart\Assert\Assert;

readonly class PresetPropertyCollectionLoader
{
    public function __construct(
        private PresetPropertyLoader $presetPropertyLoader,
    ) {
    }

    /**
     * @param array<string, mixed> $presetConfig
     *
     * @throws NonUniquePropertyKeyException
     * @throws PresetRenderingException
     */
    public function load(
        array $presetConfig,
        PropertyCollection $propertyCollection,
    ): PresetPropertyCollection {
        Assert::isMap($presetConfig);

        $presetProperties = [];

        foreach ($propertyCollection->all() as $property) {
            $presetProperties[] = $this->getPresetProperty($presetConfig, $property);
        }

        return new PresetPropertyCollection($presetProperties);
    }

    /**
     * @param array<string, mixed> $presetConfig
     *
     * @throws PresetRenderingException
     */
    private function getPresetProperty(
        array $presetConfig,
        Property $property,
    ): PresetProperty {
        return $this->presetPropertyLoader->load($presetConfig, $property);
    }
}
