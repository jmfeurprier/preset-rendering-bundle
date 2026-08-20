<?php

declare(strict_types=1);

namespace Jmf\RenderingPreset\Preset\Rendering;

use Jmf\RenderingPreset\Exception\UndefinedArrayKeyException;
use Jmf\RenderingPreset\Exception\UnexpectedItemSourceException;
use Jmf\RenderingPreset\Exception\UnreadableItemValueException;
use Jmf\RenderingPreset\Exception\UnreadableObjectPropertyException;
use Jmf\RenderingPreset\Preset\Preset;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Throwable;
use UnitEnum;

readonly class ItemValueReader
{
    public function __construct(
        private PropertyAccessorInterface $propertyAccessor,
    ) {
    }

    /**
     * @throws UnreadableItemValueException
     */
    public function read(
        Preset $preset,
        mixed $item,
        ?string $source = null,
    ): mixed {
        if (!$this->isTraversable($item)) {
            if (null === $source) {
                return $item;
            }

            throw new UnexpectedItemSourceException(
                preset: $preset,
                item:   $item,
                source: $source,
            );
        }

        $source ??= $preset->getSource();

        if (null === $source) {
            return $item;
        }

        if (is_object($item)) {
            return $this->readFromObject($preset, $item, $source);
        }

        return $this->readFromArray($preset, $item, $source);
    }

    /**
     * Enums are values, not containers: no source can be read from them.
     *
     * @phpstan-assert-if-true array<array-key, mixed>|object $item
     */
    private function isTraversable(mixed $item): bool
    {
        if ($item instanceof UnitEnum) {
            return false;
        }

        return is_array($item) || is_object($item);
    }

    /**
     * @param array<array-key, mixed> $item
     *
     * @throws UndefinedArrayKeyException
     */
    private function readFromArray(
        Preset $preset,
        array $item,
        string $source,
    ): mixed {
        if (array_key_exists($source, $item)) {
            return $item[$source];
        }

        throw new UndefinedArrayKeyException(
            preset: $preset,
            array:  $item,
            key:    $source,
        );
    }

    /**
     * @throws UnreadableObjectPropertyException
     */
    private function readFromObject(
        Preset $preset,
        object $item,
        string $source,
    ): mixed {
        try {
            return $this->propertyAccessor->getValue($item, $source);
        } catch (Throwable $e) {
            throw new UnreadableObjectPropertyException(
                preset:   $preset,
                object:   $item,
                property: $source,
                previous: $e,
            );
        }
    }
}
