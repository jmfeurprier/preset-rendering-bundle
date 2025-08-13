<?php

declare(strict_types=1);

namespace Jmf\RenderingPreset\Configuration\Property;

use Jmf\RenderingPreset\Exception\ReservedPropertyKeyException;
use Jmf\RenderingPreset\Property\Property;
use Webmozart\Assert\Assert;

readonly class PropertyLoader
{
    /**
     * @const string[]
     */
    private const iterable RESERVED_KEYS = [
        'parent',
        'source',
        'template',
    ];

    /**
     * @param non-empty-string     $propertyKey
     * @param array<string, mixed> $propertyConfig
     *
     * @throws ReservedPropertyKeyException
     */
    public function load(
        string $propertyKey,
        array $propertyConfig,
    ): Property {
        Assert::stringNotEmpty($propertyKey, 'Property Key should not be empty.');
        Assert::isMap($propertyConfig);

        if (in_array($propertyKey, self::RESERVED_KEYS, true)) {
            throw new ReservedPropertyKeyException($propertyKey);
        }

        return new Property(
            key:      $propertyKey,
            required: $this->isRequired($propertyConfig),
            default:  $this->getDefault($propertyConfig),
            choices:  $this->getChoices($propertyConfig),
        );
    }

    /**
     * @param array<string, mixed> $propertyConfig
     */
    private function isRequired(array $propertyConfig): bool
    {
        $required = $propertyConfig['required'] ?? false;

        Assert::boolean($required);

        return $required;
    }

    /**
     * @param array<string, mixed> $propertyConfig
     */
    private function getDefault(array $propertyConfig): mixed
    {
        return $propertyConfig['default'] ?? null;
    }

    /**
     * @param array<string, mixed> $propertyConfig
     *
     * @return mixed[]
     */
    private function getChoices(array $propertyConfig): iterable
    {
        $choices = $propertyConfig['choices'] ?? [];

        Assert::isIterable($choices);
        Assert::allScalar($choices);
        Assert::uniqueValues((array) $choices);

        return $choices;
    }
}
