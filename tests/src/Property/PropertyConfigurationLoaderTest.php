<?php

declare(strict_types=1);

namespace Jmf\PresetRendering\Tests\Property;

use Jmf\PresetRendering\Exception\ReservedPropertyKeyException;
use Jmf\PresetRendering\Property\PropertyLoader;
use Override;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class PropertyConfigurationLoaderTest extends TestCase
{
    private PropertyLoader $propertyConfigurationLoader;

    #[Override]
    protected function setUp(): void
    {
        $this->propertyConfigurationLoader = new PropertyLoader();
    }

    /**
     * @throws ReservedPropertyKeyException
     */
    public function testLoadBare(): void
    {
        $propertyKey    = 'foo';
        $propertyConfig = [];

        $propertyConfiguration = $this->propertyConfigurationLoader->load(
            $propertyKey,
            $propertyConfig,
        );

        self::assertSame('foo', $propertyConfiguration->getKey());
        self::assertFalse($propertyConfiguration->isRequired());
        self::assertNull($propertyConfiguration->getDefault());
        self::assertSame([], $propertyConfiguration->getChoices());
    }

    /**
     * @return array{0: array<string, mixed>, 1: bool, 2: mixed, 3: mixed[]}[]
     */
    public static function dataProviderLoad(): iterable
    {
        return [
            [
                [
                    'required' => true,
                    'default'  => 'abc',
                    'choices'  => [
                        'def',
                        'ghi',
                    ],
                ],
                true,
                'abc',
                [
                    'def',
                    'ghi',
                ],
            ],
            [
                [
                    'required' => false,
                    'default'  => 123,
                    'choices'  => [],
                ],
                false,
                123,
                [
                ],
            ],
        ];
    }

    /**
     * @param array<string, mixed> $propertyConfig
     * @param mixed[]              $choices
     *
     * @throws ReservedPropertyKeyException
     */
    #[DataProvider('dataProviderLoad')]
    public function testLoad(
        array $propertyConfig,
        bool $required,
        mixed $default,
        iterable $choices,
    ): void {
        $propertyConfiguration = $this->propertyConfigurationLoader->load(
            'foo',
            $propertyConfig,
        );

        self::assertSame($required, $propertyConfiguration->isRequired());
        self::assertSame($default, $propertyConfiguration->getDefault());
        self::assertSame($choices, $propertyConfiguration->getChoices());
    }
}
