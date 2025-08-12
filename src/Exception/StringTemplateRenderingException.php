<?php

declare(strict_types=1);

namespace Jmf\PresetRendering\Exception;

use Throwable;

class StringTemplateRenderingException extends PresetRenderingException
{
    public function __construct(
        private readonly string $string,
        ?Throwable $previous = null,
    ) {
        parent::__construct(
            message:  "Failed rendering template string: {$this->string}.",
            previous: $previous,
        );
    }

    public function getString(): string
    {
        return $this->string;
    }
}
