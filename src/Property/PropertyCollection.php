<?php

declare(strict_types=1);

namespace Jmf\RenderingPreset\Property;

use Jmf\RenderingPreset\Exception\DuplicatePropertyException;
use Webmozart\Assert\Assert;

readonly class PropertyCollection
{
    /**
     * @var array<non-empty-string, Property>
     */
    private array $properties;

    /**
     * @param Property[] $properties
     *
     * @throws DuplicatePropertyException
     */
    public function __construct(
        iterable $properties,
    ) {
        Assert::allIsInstanceOf($properties, Property::class);

        $indexed = [];

        foreach ($properties as $property) {
            $key = $property->getKey();

            if (array_key_exists($key, $indexed)) {
                throw new DuplicatePropertyException($key);
            }

            $indexed[$key] = $property;
        }

        $this->properties = $indexed;
    }

    /**
     * @return Property[]
     */
    public function all(): iterable
    {
        return array_values($this->properties);
    }
}
