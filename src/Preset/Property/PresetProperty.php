<?php

declare(strict_types=1);

namespace Jmf\PresetRendering\Preset\Property;

use Webmozart\Assert\Assert;

readonly class PresetProperty
{
    /**
     * @param non-empty-string $key
     */
    public function __construct(
        private string $key,
        private mixed $value,
    ) {
        Assert::stringNotEmpty($this->key);
    }

    /**
     * @return non-empty-string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }
}
