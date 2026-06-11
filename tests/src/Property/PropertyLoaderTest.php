<?php

declare(strict_types=1);

namespace Jmf\RenderingPreset\Tests\Property;

use Jmf\RenderingPreset\Exception\ReservedPropertyKeyException;
use Jmf\RenderingPreset\Property\PropertyLoader;
use Override;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class PropertyLoaderTest extends TestCase
{
    private PropertyLoader $propertyLoader;

    #[Override]
    protected function setUp(): void
    {
        $this->propertyLoader = new PropertyLoader();
    }

    /**
     * @throws ReservedPropertyKeyException
     */
    public function testLoadBare(): void
    {
        $propertyKey    = 'foo';
        $propertyConfig = [];

        $propertyConfiguration = $this->propertyLoader->load(
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
    public static function dataProviderLoad(): array
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
        $propertyConfiguration = $this->propertyLoader->load(
            'foo',
            $propertyConfig,
        );

        self::assertSame($required, $propertyConfiguration->isRequired());
        self::assertSame($default, $propertyConfiguration->getDefault());
        self::assertSame($choices, $propertyConfiguration->getChoices());
    }
}
