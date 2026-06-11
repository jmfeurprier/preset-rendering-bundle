<?php

declare(strict_types=1);

namespace Jmf\RenderingPreset\Preset;

use Jmf\RenderingPreset\Exception\CircularPresetParentException;
use Jmf\RenderingPreset\Exception\DuplicatePropertyException;
use Jmf\RenderingPreset\Exception\MissingRequiredPropertyValueException;
use Jmf\RenderingPreset\Exception\PresetNotFoundException;
use Jmf\RenderingPreset\Exception\PropertyValueDomainException;
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
        return $this->resolve($id, []);
    }

    /**
     * @param list<string> $loading preset ids currently being resolved in this call chain
     *
     * @throws CircularPresetParentException
     * @throws DuplicatePropertyException
     * @throws MissingRequiredPropertyValueException
     * @throws PresetNotFoundException
     * @throws PropertyValueDomainException
     * @throws ReservedPropertyKeyException
     */
    private function resolve(
        string $id,
        array $loading,
    ): Preset {
        if (array_key_exists($id, $this->presets)) {
            return $this->presets[$id];
        }

        if (!array_key_exists($id, $this->presetsConfig)) {
            throw new PresetNotFoundException($id);
        }

        if (in_array($id, $loading, true)) {
            throw new CircularPresetParentException(
                [
                    ...$loading,
                    $id,
                ],
            );
        }

        $loading[] = $id;

        // Only the requested preset and its parent chain are built; shared parents are built once
        // because the resolver routes back through this memoized presets cache.
        return $this->presets[$id] = $this->presetLoader->load(
            $this->presetsConfig,
            $id,
            $this->getPropertyCollection(),
            fn(
                string $parentId,
            ): Preset => $this->resolve($parentId, $loading),
        );
    }

    /**
     * @throws DuplicatePropertyException
     * @throws ReservedPropertyKeyException
     */
    private function getPropertyCollection(): PropertyCollection
    {
        return $this->propertyCollection ??= $this->propertyRepository->getCollection();
    }
}
