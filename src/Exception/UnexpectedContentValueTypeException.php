<?php

declare(strict_types=1);

namespace Jmf\RenderingPreset\Exception;

use Jmf\RenderingPreset\Preset\Preset;

class UnexpectedContentValueTypeException extends RenderingPresetException
{
    /**
     * @param array<string, mixed>|object $item
     */
    public function __construct(
        private readonly Preset $preset,
        private readonly array | object $item,
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

    /**
     * @return array<string, mixed>|object
     */
    public function getItem(): array | object
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
