<?php

declare(strict_types=1);

namespace Jmf\RenderingPreset\Exception;

class DuplicatePropertyException extends InvalidConfigurationException
{
    public function __construct(string $key)
    {
        parent::__construct("Duplicate property configuration for '{$key}'.");
    }
}
