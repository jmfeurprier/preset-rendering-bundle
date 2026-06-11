<?php

declare(strict_types=1);

namespace Jmf\RenderingPreset\Preset;

use Jmf\RenderingPreset\Exception\CircularPresetParentException;
use Jmf\RenderingPreset\Exception\InvalidPresetConfigurationException;
use Jmf\RenderingPreset\Exception\MissingRequiredPropertyValueException;
use Jmf\RenderingPreset\Exception\PresetNotFoundException;
use Jmf\RenderingPreset\Exception\PropertyValueDomainException;
use Jmf\RenderingPreset\Property\PropertyCollection;
use Override;
use Webmozart\Assert\Assert;

class PresetRepository implements PresetRepositoryInterface
{
    /**
     * @var array<string, Preset>
     */
    private array $presets = [];

    /**
     * @param array<string, array<string, mixed>> $presetsConfig
     */
    public function __construct(
        private readonly PresetLoader $presetLoader,
        private readonly PropertyCollection $propertyCollection,
        private readonly array $presetsConfig,
    ) {
    }

    /**
     * @param non-empty-string $id
     */
    #[Override]
    public function get(string $id): Preset
    {
        Assert::stringNotEmpty($id);

        return $this->resolve($id, []);
    }

    /**
     * @param non-empty-string $id
     * @param list<string>     $loading preset ids currently being resolved in this call chain
     *
     * @throws CircularPresetParentException
     * @throws InvalidPresetConfigurationException
     * @throws MissingRequiredPropertyValueException
     * @throws PresetNotFoundException
     * @throws PropertyValueDomainException
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

        $presetConfig = $this->presetsConfig[$id];

        // Only the requested preset and its parent chain are built; shared parents are built once
        // because the resolver routes back through this memoized presets cache.
        return $this->presets[$id] = $this->presetLoader->load(
            $presetConfig,
            $id,
            $this->propertyCollection,
            function (string $parentId) use ($loading): Preset {
                Assert::stringNotEmpty($parentId);

                return $this->resolve($parentId, $loading);
            },
        );
    }
}
