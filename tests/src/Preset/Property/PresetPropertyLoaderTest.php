<?php

declare(strict_types=1);

namespace Jmf\PresetRendering\Tests\Preset\Property;

use Jmf\PresetRendering\Exception\PresetRenderingException;
use Jmf\PresetRendering\Preset\Property\PresetPropertyLoader;
use Jmf\PresetRendering\Property\Property;
use Override;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class PresetPropertyLoaderTest extends TestCase
{
    private PresetPropertyLoader $presetPropertyLoader;

    #[Override]
    protected function setUp(): void
    {
        $this->presetPropertyLoader = new PresetPropertyLoader();
    }

    /**
     * @throws PresetRenderingException
     */
    public function testLoadWithNullDefaultValue(): void
    {
        $property = new Property(
            key:      'foo',
            required: false,
        );

        $config = [];

        $presetProperty = $this->presetPropertyLoader->load($config, $property);

        self::assertSame('foo', $presetProperty->getKey());
        self::assertNull($presetProperty->getValue());
    }

    /**
     * @throws PresetRenderingException
     */
    public function testLoadWithSpecificDefaultValue(): void
    {
        $property = new Property(
            key:      'foo',
            required: false,
            default:  'bar',
        );

        $config = [];

        $presetProperty = $this->presetPropertyLoader->load($config, $property);

        self::assertSame('foo', $presetProperty->getKey());
        self::assertSame('bar', $presetProperty->getValue());
    }

    /**
     * @return array{0: non-empty-string, 1: mixed, 2: bool, 3?: mixed, 4?: mixed[]}[]
     */
    public static function dataProviderValidCases(): iterable
    {
        return [
            [
                'foo',
                'baz',
                true,
                'qux',
                [
                    'bar',
                    'baz',
                    'qux',
                ],
            ],
            [
                'foo',
                'baz',
                true,
            ],
        ];
    }

    /**
     * @param non-empty-string $key
     * @param mixed[]          $choices
     *
     * @throws PresetRenderingException
     */
    #[DataProvider('dataProviderValidCases')]
    public function testLoadWithDefinedConfigValue(
        string $key,
        mixed $value,
        bool $required,
        mixed $default = null,
        iterable $choices = [],
    ): void {
        $property = new Property(
            key:      $key,
            required: $required,
            default:  $default,
            choices:  $choices,
        );

        $config = [
            $key => $value,
        ];

        $presetProperty = $this->presetPropertyLoader->load($config, $property);

        self::assertSame($key, $presetProperty->getKey());
        self::assertSame($value, $presetProperty->getValue());
    }
}
