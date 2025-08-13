<?php

declare(strict_types=1);

namespace Jmf\RenderingPreset\Preset;

use Jmf\RenderingPreset\Configuration\Preset\PresetCollectionLoader;
use Override;

class PresetRepository implements PresetRepositoryInterface
{
    private PresetCollection $presetCollection;

    /**
     * @param array<string, array<string, mixed>> $presetsConfig
     */
    public function __construct(
        protected readonly PresetCollectionLoader $presetCollectionLoader,
        protected readonly array $presetsConfig,
    ) {
    }

    #[Override]
    public function getCollection(): PresetCollection
    {
        if (!isset($this->presetCollection)) {
            $this->presetCollection = $this->presetCollectionLoader->load($this->presetsConfig);
        }

        return $this->presetCollection;
    }
}
