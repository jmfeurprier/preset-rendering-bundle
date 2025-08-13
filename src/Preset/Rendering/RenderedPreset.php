<?php

declare(strict_types=1);

namespace Jmf\RenderingPreset\Preset\Rendering;

use Jmf\RenderingPreset\Preset\Property\PresetPropertyCollection;

readonly class RenderedPreset
{
    public function __construct(
        private string $content,
        private PresetPropertyCollection $properties,
    ) {
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getProperties(): PresetPropertyCollection
    {
        return $this->properties;
    }
}
