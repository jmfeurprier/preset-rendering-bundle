<?php

declare(strict_types=1);

namespace Jmf\RenderingPreset\Property;

use Webmozart\Assert\Assert;

readonly class Property
{
    /**
     * @param non-empty-string $key
     * @param mixed[]          $choices
     */
    public function __construct(
        private string $key,
        private bool $required,
        private mixed $default = null,
        private iterable $choices = [],
    ) {
        Assert::stringNotEmpty($this->key, 'Property Key cannot be an empty string.');

        // @todo Validate $default type.
        // @todo Validate $choices types + unicity + emptiness.
    }

    /**
     * @return non-empty-string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function getDefault(): mixed
    {
        return $this->default;
    }

    /**
     * @return mixed[]
     */
    public function getChoices(): iterable
    {
        return $this->choices;
    }
}
