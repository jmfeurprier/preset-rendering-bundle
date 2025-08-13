<?php

declare(strict_types=1);

namespace Jmf\RenderingPreset\Configuration\Preset\Property;

use Jmf\RenderingPreset\Preset\Property\PresetPropertyCollection;

readonly class PresetPropertyCollectionMerger
{
    public function merge(
        PresetPropertyCollection $lhs,
        PresetPropertyCollection $rhs,
    ): PresetPropertyCollection {
        $merged = [];

        foreach ($lhs->all() as $property) {
            $merged[$property->getKey()] = $property;
        }

        foreach ($rhs->all() as $property) {
            $merged[$property->getKey()] = $property;
        }

        return new PresetPropertyCollection(array_values($merged));
    }
}
