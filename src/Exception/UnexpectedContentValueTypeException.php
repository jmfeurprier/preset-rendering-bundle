<?php

declare(strict_types=1);

namespace Jmf\RenderingPreset\Exception;

use Jmf\RenderingPreset\Preset\Preset;

class UnexpectedContentValueTypeException extends RenderingPresetException
{
    public function __construct(
        private readonly Preset $preset,
        private readonly mixed $item,
        private readonly ?string $source,
        private readonly mixed $value,
    ) {
        parent::__construct(
            message: sprintf(
                         'Unexpected item content value type (preset: %s).',
                         $this->preset->getId(),
                     ),
        );
    }

    public function getPreset(): Preset
    {
        return $this->preset;
    }

    public function getItem(): mixed
    {
        return $this->item;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }
}
