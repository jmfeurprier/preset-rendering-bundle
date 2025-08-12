<?php

declare(strict_types=1);

namespace Jmf\PresetRendering\Preset;

use Jmf\PresetRendering\Exception\PresetNotFoundException;
use Jmf\PresetRendering\Exception\PresetRenderingException;
use Webmozart\Assert\Assert;

readonly class PresetCollection
{
    /**
     * @var array<non-empty-string, Preset>
     */
    private array $presets;

    /**
     * @param Preset[] $presets
     *
     * @throws PresetRenderingException
     */
    public function __construct(
        iterable $presets,
    ) {
        Assert::allIsInstanceOf($presets, Preset::class);

        $indexed = [];

        foreach ($presets as $preset) {
            $id = $preset->getId();

            if (array_key_exists($id, $indexed)) {
                // @todo
                throw new PresetRenderingException("Non-unique Preset Id '{$id}' found .");
            }

            $indexed[$id] = $preset;
        }

        $this->presets = $indexed;
    }

    /**
     * @throws PresetNotFoundException
     */
    public function get(string $id): Preset
    {
        if (!array_key_exists($id, $this->presets)) {
            throw new PresetNotFoundException($id);
        }

        return $this->presets[$id];
    }
}
