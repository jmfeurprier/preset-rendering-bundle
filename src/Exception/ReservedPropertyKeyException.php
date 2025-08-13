<?php

declare(strict_types=1);

namespace Jmf\RenderingPreset\Exception;

class ReservedPropertyKeyException extends InvalidConfigurationException
{
    public function __construct(
        private readonly string $key,
    ) {
        parent::__construct("Property Key '{$this->key}' is a reserved keyword.");
    }
}
