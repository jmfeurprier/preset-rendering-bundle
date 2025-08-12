<?php

declare(strict_types=1);

namespace Jmf\PresetRendering\Property;

use Override;

class PropertyRepository implements PropertyRepositoryInterface
{
    private PropertyCollection $propertyCollection;

    /**
     * @param array<string, array<string, mixed>> $propertiesConfig
     */
    public function __construct(
        private readonly PropertyCollectionLoader $propertyCollectionLoader,
        private readonly array $propertiesConfig,
    ) {
    }

    #[Override]
    public function getCollection(): PropertyCollection
    {
        if (!isset($this->propertyCollection)) {
            $this->propertyCollection = $this->propertyCollectionLoader->load(
                $this->propertiesConfig,
            );
        }

        return $this->propertyCollection;
    }
}
