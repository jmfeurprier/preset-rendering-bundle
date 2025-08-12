<?php

declare(strict_types=1);

namespace Jmf\PresetRendering\Exception;

class NonUniquePropertyKeyException extends PresetRenderingException
{
    public function __construct(
        private readonly string $key,
    ) {
        parent::__construct("Property Key '{$this->key}' is not unique.");
    }
}
