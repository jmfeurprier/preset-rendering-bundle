<?php

declare(strict_types=1);

namespace Jmf\RenderingPreset\Preset;

use Jmf\RenderingPreset\Configuration\Preset\PresetLoader;
use Jmf\RenderingPreset\Exception\PresetNotFoundException;
use Jmf\RenderingPreset\Exception\ReservedPropertyKeyException;
use Jmf\RenderingPreset\Property\PropertyCollection;
use Jmf\RenderingPreset\Property\PropertyRepositoryInterface;
use Override;

class PresetRepository implements PresetRepositoryInterface
{
    /**
     * @var array<string, Preset>
     */
    private array $presets = [];

    private PropertyCollection $propertyCollection;

    /**
     * @param array<string, array<string, mixed>> $presetsConfig
     */
    public function __construct(
        private readonly PresetLoader $presetLoader,
        private readonly PropertyRepositoryInterface $propertyRepository,
        private readonly array $presetsConfig,
    ) {
    }

    #[Override]
    public function get(string $id): Preset
    {
        if (isset($this->presets[$id])) {
            return $this->presets[$id];
        }

        if (!array_key_exists($id, $this->presetsConfig)) {
            throw new PresetNotFoundException($id);
        }

        // Only the requested preset and its parent chain are built; shared parents are built once
        // because the resolver routes back through this memoized get().
        return $this->presets[$id] = $this->presetLoader->load(
            $this->presetsConfig,
            $id,
            $this->propertyCollection(),
            fn (string $parentId): Preset => $this->get($parentId),
        );
    }

    #[Override]
    public function getCollection(): PresetCollection
    {
        foreach (array_keys($this->presetsConfig) as $id) {
            $this->get($id);
        }

        return new PresetCollection($this->presets);
    }

    /**
     * @throws ReservedPropertyKeyException
     */
    private function propertyCollection(): PropertyCollection
    {
        return $this->propertyCollection ??= $this->propertyRepository->getCollection();
    }
}
