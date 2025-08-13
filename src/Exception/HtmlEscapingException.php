<?php

declare(strict_types=1);

namespace Jmf\RenderingPreset\Exception;

use Throwable;

class HtmlEscapingException extends RenderingPresetException
{
    public function __construct(
        private readonly string $value,
        ?Throwable $previous = null,
    ) {
        parent::__construct(
            message:  'Failed HTML-escaping provided value.',
            previous: $previous,
        );
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
