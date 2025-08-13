<?php

declare(strict_types=1);

namespace Jmf\RenderingPreset\Configuration\Preset;

use Jmf\RenderingPreset\Exception\MissingRequiredPropertyValueException;
use Jmf\RenderingPreset\Exception\PropertyValueDomainException;
use Jmf\RenderingPreset\Exception\ReservedPropertyKeyException;
use Jmf\RenderingPreset\Preset\PresetCollection;
use Jmf\RenderingPreset\Property\PropertyRepositoryInterface;
use Webmozart\Assert\Assert;

readonly class PresetCollectionLoader
{
    public function __construct(
        private PropertyRepositoryInterface $propertyRepository,
        private PresetLoader $presetLoader,
    ) {
    }

    /**
     * @param array<string, mixed> $presetsConfig
     *
     * @throws MissingRequiredPropertyValueException
     * @throws PropertyValueDomainException
     * @throws ReservedPropertyKeyException
     */
    public function load(
        array $presetsConfig,
    ): PresetCollection {
        Assert::isMap($presetsConfig);

        $propertyCollection = $this->propertyRepository->getCollection();
        $presets            = [];

        foreach (array_keys($presetsConfig) as $presetId) {
            Assert::stringNotEmpty($presetId);

            $presets[] = $this->presetLoader->load(
                $presetsConfig,
                $presetId,
                $propertyCollection,
            );
        }

        return new PresetCollection($presets);
    }
}
