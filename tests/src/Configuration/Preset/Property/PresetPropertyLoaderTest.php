<?php

declare(strict_types=1);

namespace Jmf\RenderingPreset\Tests\Configuration\Preset\Property;

use Jmf\RenderingPreset\Configuration\Preset\Property\PresetPropertyLoader;
use Jmf\RenderingPreset\Exception\RenderingPresetException;
use Jmf\RenderingPreset\Property\Property;
use Override;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class PresetPropertyLoaderTest extends TestCase
{
    private PresetPropertyLoader $presetPropertyLoader;

    #[Override]
    protected function setUp(): void
    {
        $this->presetPropertyLoader = new \Jmf\RenderingPreset\Configuration\Preset\Property\PresetPropertyLoader();
    }

    /**
     * @throws RenderingPresetException
     */
    public function testLoadWithNullDefaultValue(): void
    {
        $presetId = 'abc';

        $property = new Property(
            key:      'foo',
            required: false,
        );

        $config = [];

        $presetProperty = $this->presetPropertyLoader->load($presetId, $config, $property);

        self::assertSame('foo', $presetProperty->getKey());
        self::assertNull($presetProperty->getValue());
    }

    /**
     * @throws RenderingPresetException
     */
    public function testLoadWithSpecificDefaultValue(): void
    {
        $presetId = 'abc';

        $property = new Property(
            key:      'foo',
            required: false,
            default:  'bar',
        );

        $config = [];

        $presetProperty = $this->presetPropertyLoader->load($presetId, $config, $property);

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
     * @throws RenderingPresetException
     */
    #[DataProvider('dataProviderValidCases')]
    public function testLoadWithDefinedConfigValue(
        string $key,
        mixed $value,
        bool $required,
        mixed $default = null,
        iterable $choices = [],
    ): void {
        $presetId = 'abc';

        $property = new Property(
            key:      $key,
            required: $required,
            default:  $default,
            choices:  $choices,
        );

        $config = [
            $key => $value,
        ];

        $presetProperty = $this->presetPropertyLoader->load($presetId, $config, $property);

        self::assertSame($key, $presetProperty->getKey());
        self::assertSame($value, $presetProperty->getValue());
    }
}
