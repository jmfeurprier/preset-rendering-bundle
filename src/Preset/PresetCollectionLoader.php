<?php

declare(strict_types=1);

namespace Jmf\PresetRendering\Preset;

use Jmf\PresetRendering\Property\PropertyRepositoryInterface;
use Override;
use Webmozart\Assert\Assert;

readonly class PresetCollectionLoader implements PresetCollectionLoaderInterface
{
    public function __construct(
        private PropertyRepositoryInterface $propertyRepository,
        private PresetLoader $presetLoader,
    ) {
    }

    #[Override]
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
