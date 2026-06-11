<?php

declare(strict_types=1);

namespace Jmf\RenderingPreset\Exception;

use Exception;
use Throwable;

abstract class RenderingPresetException extends Exception
{
    protected function __construct(
        string $message,
        ?Throwable $previous = null,
    ) {
        parent::__construct(
            message:  $message,
            previous: $previous,
        );
    }
}
