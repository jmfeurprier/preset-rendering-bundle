<?php

declare(strict_types=1);

namespace Jmf\RenderingPreset\Preset;

use Jmf\RenderingPreset\Exception\InvalidConfigurationException;

interface PresetRepositoryInterface
{
    /**
     * @throws InvalidConfigurationException
     */
    public function getCollection(): PresetCollection;
}
