<?php

declare(strict_types=1);

namespace Jmf\PresetRendering\Preset\Property;

use Jmf\PresetRendering\Exception\NonUniquePropertyKeyException;
use Jmf\PresetRendering\Exception\PresetPropertyNotFoundException;
use Webmozart\Assert\Assert;

readonly class PresetPropertyCollection
{
    /**
     * @var array<non-empty-string, PresetProperty>
     */
    private array $properties;

    /**
     * @param PresetProperty[] $properties
     *
     * @throws NonUniquePropertyKeyException
     */
    public function __construct(
        iterable $properties,
    ) {
        Assert::allIsInstanceOf($properties, PresetProperty::class);

        $indexed = [];

        foreach ($properties as $property) {
            $key = $property->getKey();

            if (array_key_exists($key, $indexed)) {
                throw new NonUniquePropertyKeyException($key);
            }

            $indexed[$key] = $property;
        }

        $this->properties = $indexed;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->properties);
    }

    /**
     * @throws PresetPropertyNotFoundException
     */
    public function getValue(string $key): mixed
    {
        return $this->get($key)->getValue();
    }

    public function tryGetValue(
        string $key,
        mixed $default = null,
    ): mixed {
        return $this->tryGet($key)?->getValue() ?? $default;
    }

    /**
     * @throws PresetPropertyNotFoundException
     */
    public function get(string $key): PresetProperty
    {
        return $this->properties[$key]
            ??
            throw new PresetPropertyNotFoundException($key);
    }

    public function tryGet(string $key): ?PresetProperty
    {
        return $this->properties[$key]
            ??
            null;
    }

    /**
     * @return PresetProperty[]
     */
    public function all(): iterable
    {
        return array_values($this->properties);
    }

    /**
     * @throws NonUniquePropertyKeyException
     */
    public function merge(self $other): self
    {
        return new self(
            array_values(
                array_merge(
                    $other->properties,
                    $this->properties,
                ),
            ),
        );
    }
}
