<?php

declare(strict_types=1);

namespace Jmf\RenderingPreset\Preset;

use Jmf\RenderingPreset\Property\PropertyCollectionLoader;

readonly class PresetRepositoryFactory
{
    public function __construct(
        private PresetLoader $presetLoader,
        private PropertyCollectionLoader $propertyCollectionLoader,
    ) {
    }

    /**
     * @param array<string, array<string, mixed>> $presetsConfig
     * @param array<string, mixed>                $propertiesConfig
     */
    public function create(
        array $presetsConfig,
        array $propertiesConfig,
    ): PresetRepository {
        return new PresetRepository(
            $this->presetLoader,
            $this->propertyCollectionLoader->load($propertiesConfig),
            $presetsConfig,
        );
    }
}
