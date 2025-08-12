<?php

declare(strict_types=1);

namespace Jmf\PresetRendering\Exception;

use Jmf\PresetRendering\Preset\Preset;
use Throwable;

class TemplateRenderingException extends PresetRenderingException
{
    public function __construct(
        Preset $preset,
        ?Throwable $previous = null,
    ) {
        parent::__construct(
            message:  "Failed rendering Preset '{$preset->getId()}' Template.",
            previous: $previous,
        );
    }
}
