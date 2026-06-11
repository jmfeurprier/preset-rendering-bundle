<?php

declare(strict_types=1);

namespace Jmf\RenderingPreset\Preset;

use Jmf\RenderingPreset\Exception\InvalidConfigurationException;
use Jmf\RenderingPreset\Exception\PresetNotFoundException;

interface PresetRepositoryInterface
{
    /**
     * Resolves a single preset (and its parent chain) on demand, building only what is requested.
     *
     * @throws PresetNotFoundException
     * @throws InvalidConfigurationException
     */
    public function get(string $id): Preset;

    /**
     * @throws PresetNotFoundException
     * @throws InvalidConfigurationException
     */
    public function getCollection(): PresetCollection;
}
