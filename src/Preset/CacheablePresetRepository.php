<?php

declare(strict_types=1);

namespace Jmf\RenderingPreset\Preset;

use Override;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Webmozart\Assert\Assert;

readonly class CacheablePresetRepository implements PresetRepositoryInterface
{
    /**
     * @param array<string, array<string, mixed>> $presetsConfig
     */
    public function __construct(
        private PresetRepositoryInterface $wrapped,
        private array $presetsConfig,
        private CacheInterface $cache,
    ) {
    }

    /**
     * @throws InvalidArgumentException
     */
    #[Override]
    public function getCollection(): PresetCollection
    {
        $presetCollection = $this->cache->get(
            $this->getCacheKey(),
            fn(
                ItemInterface $item,
            ): PresetCollection => $this->wrapped->getCollection(),
        );

        Assert::isInstanceOf($presetCollection, PresetCollection::class);

        return $presetCollection;
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
                    $this->presetsConfig,
                ],
            ),
        );
    }
}
