<?php

declare(strict_types=1);

namespace Jmf\RenderingPreset\Property;

use Jmf\RenderingPreset\Exception\DuplicatePropertyException;
use Jmf\RenderingPreset\Exception\ReservedPropertyKeyException;
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
        return $this->propertyCollection ??= $this->loadCollection();
    }

    /**
     * @throws DuplicatePropertyException
     * @throws ReservedPropertyKeyException
     */
    private function loadCollection(): PropertyCollection
    {
        return $this->propertyCollectionLoader->load($this->propertiesConfig);
    }
}
