<?php

declare(strict_types=1);

namespace Jmf\PresetRendering\Preset;

use Jmf\PresetRendering\Exception\NonUniquePropertyKeyException;
use Jmf\PresetRendering\Exception\PresetNotFoundException;
use Jmf\PresetRendering\Exception\PresetRenderingException;
use Jmf\PresetRendering\Exception\ReservedPropertyKeyException;

interface PresetRepositoryInterface
{
    /**
     * @throws NonUniquePropertyKeyException
     * @throws PresetNotFoundException
     * @throws PresetRenderingException
     * @throws ReservedPropertyKeyException
     */
    public function get(string $id): Preset;
}
