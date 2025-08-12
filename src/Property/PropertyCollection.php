<?php

declare(strict_types=1);

namespace Jmf\PresetRendering\Property;

use Webmozart\Assert\Assert;

readonly class PropertyCollection
{
    /**
     * @var array<non-empty-string, Property>
     */
    private array $properties;

    /**
     * @param Property[] $properties
     */
    public function __construct(
        iterable $properties,
    ) {
        Assert::allIsInstanceOf($properties, Property::class);

        $indexed = [];

        foreach ($properties as $property) {
            // @todo Validate key unicity.
            $indexed[$property->getKey()] = $property;
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
