<?php

declare(strict_types=1);

namespace Jmf\RenderingPreset\Exception;

use Jmf\RenderingPreset\Preset\Preset;

class UnexpectedItemSourceException extends UnreadableItemValueException
{
    public function __construct(
        Preset $preset,
        private readonly mixed $item,
        private readonly string $source,
    ) {
        parent::__construct(
            preset:  $preset,
            message: sprintf(
                         "Cannot read source '%s' from %s item in preset '%s'.",
                         $this->source,
                         get_debug_type($this->item),
                         $preset->getId(),
                     ),
        );
    }

    public function getItem(): mixed
    {
        return $this->item;
    }

    public function getSource(): string
    {
        return $this->source;
    }
}
