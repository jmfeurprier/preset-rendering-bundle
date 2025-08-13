<?php

declare(strict_types=1);

namespace Jmf\RenderingPreset\Configuration\Preset;

use Jmf\RenderingPreset\Configuration\Preset\Property\PresetPropertyCollectionMerger;
use Jmf\RenderingPreset\Preset\Preset;
use Jmf\RenderingPreset\Preset\Property\PresetPropertyCollection;

readonly class PresetMerger
{
    public function __construct(
        private PresetPropertyCollectionMerger $presetPropertyCollectionMerger,
    ) {
    }

    public function merge(
        Preset $preset,
        Preset $parent,
    ): Preset {
        return new Preset(
            id:         $preset->getId(),
            source:     $preset->getSource() ?? $parent->getSource(),
            template:   $preset->getTemplate() ?? $parent->getTemplate(),
            properties: $this->mergeProperties($preset, $parent),
        );
    }

    private function mergeProperties(
        Preset $preset,
        Preset $parent,
    ): PresetPropertyCollection {
        return $this->presetPropertyCollectionMerger->merge(
            $parent->getProperties(),
            $preset->getProperties(),
        );
    }
}
