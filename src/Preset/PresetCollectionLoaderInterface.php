<?php

declare(strict_types=1);

namespace Jmf\PresetRendering\Preset;

use Jmf\PresetRendering\Exception\NonUniquePropertyKeyException;
use Jmf\PresetRendering\Exception\PresetNotFoundException;
use Jmf\PresetRendering\Exception\PresetRenderingException;
use Jmf\PresetRendering\Exception\ReservedPropertyKeyException;

interface PresetCollectionLoaderInterface
{
    /**
     * @param array<string, array<string, mixed>> $presetsConfig
     *
     * @throws NonUniquePropertyKeyException
     * @throws PresetNotFoundException
     * @throws PresetRenderingException
     * @throws ReservedPropertyKeyException
     */
    public function load(
        array $presetsConfig,
    ): PresetCollection;
}
