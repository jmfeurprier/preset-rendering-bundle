<?php

declare(strict_types=1);

namespace Jmf\RenderingPreset\Property;

use Jmf\RenderingPreset\Exception\DuplicatePropertyException;
use Jmf\RenderingPreset\Exception\ReservedPropertyKeyException;
use Webmozart\Assert\Assert;

readonly class PropertyCollectionLoader
{
    public function __construct(
        private PropertyLoader $propertyLoader,
    ) {
    }

    /**
     * @param array<string, mixed> $propertiesConfig
     *
     * @throws DuplicatePropertyException
     * @throws ReservedPropertyKeyException
     */
    public function load(array $propertiesConfig): PropertyCollection
    {
        Assert::isMap($propertiesConfig);

        $properties = [];

        foreach ($propertiesConfig as $propertyKey => $propertyConfig) {
            Assert::stringNotEmpty($propertyKey);
            Assert::isMap($propertyConfig);

            $properties[] = $this->propertyLoader->load(
                $propertyKey,
                $propertyConfig,
            );
        }

        return new PropertyCollection(
            $properties,
        );
    }
}
