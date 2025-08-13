<?php

declare(strict_types=1);

namespace Jmf\RenderingPreset\Exception;

use Jmf\RenderingPreset\Preset\Preset;
use Throwable;

abstract class UnreadableItemValueException extends RenderingPresetException
{
    public function __construct(
        private readonly Preset $preset,
        string $message,
        ?Throwable $previous = null,
    ) {
        parent::__construct(
            message:  $message,
            previous: $previous,
        );
    }

    public function getPreset(): Preset
    {
        return $this->preset;
    }
}
