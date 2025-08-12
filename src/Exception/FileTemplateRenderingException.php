<?php

declare(strict_types=1);

namespace Jmf\PresetRendering\Exception;

use Throwable;

class FileTemplateRenderingException extends PresetRenderingException
{
    public function __construct(
        private readonly string $path,
        ?Throwable $previous = null,
    ) {
        parent::__construct(
            message:  "Failed rendering preset template file at {$this->path}.",
            previous: $previous,
        );
    }

    public function getPath(): string
    {
        return $this->path;
    }
}
