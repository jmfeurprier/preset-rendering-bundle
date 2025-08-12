<?php

declare(strict_types=1);

namespace Jmf\PresetRendering\Preset\Rendering;

use Jmf\PresetRendering\Exception\UndefinedArrayKeyException;
use Jmf\PresetRendering\Exception\UnreadableObjectPropertyException;
use Jmf\PresetRendering\Preset\Preset;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Throwable;

readonly class ItemValueReader
{
    public function __construct(
        private PropertyAccessorInterface $propertyAccessor,
    ) {
    }

    /**
     * @param array<string, mixed>|object $item
     *
     * @throws UnreadableObjectPropertyException
     * @throws UndefinedArrayKeyException
     */
    public function read(
        Preset $preset,
        array | object $item,
        ?string $source = null,
    ): mixed {
        if (null === $source) {
            $source = $preset->getSource();

            if (null === $source) {
                return null;
            }
        }

        if (is_array($item)) {
            return $this->readFromArray($preset, $item, $source);
        }

        return $this->readFromObject($preset, $item, $source);
    }

    /**
     * @param array<string, mixed> $item
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
