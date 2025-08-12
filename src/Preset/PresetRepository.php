<?php

declare(strict_types=1);

namespace Jmf\PresetRendering\Preset;

use Jmf\PresetRendering\Exception\NonUniquePropertyKeyException;
use Jmf\PresetRendering\Exception\PresetNotFoundException;
use Jmf\PresetRendering\Exception\PresetRenderingException;
use Jmf\PresetRendering\Exception\ReservedPropertyKeyException;
use Override;

class PresetRepository implements PresetRepositoryInterface
{
    private PresetCollection $presetCollection;

    /**
     * @param array<string, array<string, mixed>> $presetsConfig
     */
    public function __construct(
        private readonly PresetCollectionLoaderInterface $presetCollectionLoader,
        private readonly array $presetsConfig,
    ) {
    }

    #[Override]
    public function get(string $id): Preset
    {
        return $this->getPresetCollection()->get($id);
    }

    /**
     * @throws NonUniquePropertyKeyException
     * @throws PresetNotFoundException
     * @throws PresetRenderingException
     * @throws ReservedPropertyKeyException
     */
    private function getPresetCollection(): PresetCollection
    {
        if (!isset($this->presetCollection)) {
            $this->presetCollection = $this->presetCollectionLoader->load(
                $this->presetsConfig,
            );
        }

        return $this->presetCollection;
    }
}
