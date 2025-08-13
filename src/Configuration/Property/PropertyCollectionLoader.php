<?php

declare(strict_types=1);

namespace Jmf\RenderingPreset\Configuration\Property;

use Jmf\RenderingPreset\Exception\ReservedPropertyKeyException;
use Jmf\RenderingPreset\Property\PropertyCollection;
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
