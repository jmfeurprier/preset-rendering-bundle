<?php

declare(strict_types=1);

namespace Jmf\PresetRendering\Preset;

use Override;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Webmozart\Assert\Assert;

readonly class CacheablePresetCollectionLoader implements PresetCollectionLoaderInterface
{
    public function __construct(
        private PresetCollectionLoaderInterface $wrapped,
        private CacheInterface $cache,
    ) {
    }

    /**
     * @throws InvalidArgumentException
     */
    #[Override]
    public function load(
        array $presetsConfig,
    ): PresetCollection {
        $presetCollection = $this->cache->get(
            $this->getCacheKey($presetsConfig),
            fn(
                ItemInterface $item,
            ): PresetCollection => $this->wrapped->load($presetsConfig),
        );

        Assert::isInstanceOf($presetCollection, PresetCollection::class);

        return $presetCollection;
    }

    /**
     * @param array<string, mixed> $presetsConfig
     *
     * @return non-empty-string
     */
    private function getCacheKey(
        array $presetsConfig,
    ): string {
        return md5(
            serialize(
                [
                    self::class,
                    $presetsConfig,
                ],
            ),
        );
    }
}
