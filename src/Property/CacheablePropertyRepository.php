<?php

declare(strict_types=1);

namespace Jmf\RenderingPreset\Property;

use Override;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Webmozart\Assert\Assert;

readonly class CacheablePropertyRepository implements PropertyRepositoryInterface
{
    /**
     * @param array<string, array<string, mixed>> $propertiesConfig
     */
    public function __construct(
        private PropertyRepositoryInterface $wrapped,
        private array $propertiesConfig,
        private CacheInterface $cache,
    ) {
    }

    /**
     * @throws InvalidArgumentException
     */
    #[Override]
    public function getCollection(): PropertyCollection
    {
        $propertyCollection = $this->cache->get(
            $this->getCacheKey(),
            fn(
                ItemInterface $item,
            ): PropertyCollection => $this->wrapped->getCollection(),
        );

        Assert::isInstanceOf($propertyCollection, PropertyCollection::class);

        return $propertyCollection;
    }

    /**
     * @return non-empty-string
     */
    private function getCacheKey(): string
    {
        return md5(
            serialize(
                [
                    self::class,
                    $this->propertiesConfig,
                ],
            ),
        );
    }
}
