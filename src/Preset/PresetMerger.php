<?php

declare(strict_types=1);

namespace Jmf\PresetRendering\Preset;

use Jmf\PresetRendering\Exception\NonUniquePropertyKeyException;
use Jmf\PresetRendering\Preset\Property\PresetPropertyCollection;

readonly class PresetMerger
{
    /**
     * @throws NonUniquePropertyKeyException
     */
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

    /**
     * @throws NonUniquePropertyKeyException
     */
    private function mergeProperties(
        Preset $preset,
        Preset $parent,
    ): PresetPropertyCollection {
        return $preset->getProperties()->merge(
            $parent->getProperties(),
        );
    }
}
