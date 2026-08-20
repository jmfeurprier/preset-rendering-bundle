<?php

declare(strict_types=1);

namespace Jmf\RenderingPreset\Tests\Preset\Rendering;

use Jmf\RenderingPreset\Exception\UndefinedArrayKeyException;
use Jmf\RenderingPreset\Exception\UnexpectedItemSourceException;
use Jmf\RenderingPreset\Exception\UnreadableObjectPropertyException;
use Jmf\RenderingPreset\Preset\Preset;
use Jmf\RenderingPreset\Preset\Property\PresetPropertyCollection;
use Jmf\RenderingPreset\Preset\Rendering\ItemValueReader;
use Jmf\RenderingPreset\Tests\Fixture\PureEnum;
use Jmf\RenderingPreset\Tests\Fixture\StatusEnum;
use Override;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use UnitEnum;

final class ItemValueReaderTest extends TestCase
{
    private PropertyAccessorInterface & Stub $propertyAccessor;

    private ItemValueReader $itemValueReader;

    #[Override]
    protected function setUp(): void
    {
        $this->propertyAccessor = self::createStub(PropertyAccessorInterface::class);
        $this->itemValueReader  = new ItemValueReader($this->propertyAccessor);
    }

    public function testReadReturnsArrayItemWhenNoSource(): void
    {
        $preset = $this->makePreset(null);
        $item   = ['name' => 'Alice'];

        $result = $this->itemValueReader->read($preset, $item);

        self::assertSame($item, $result);
    }

    public function testReadReturnsObjectItemWhenNoSource(): void
    {
        $preset = $this->makePreset(null);
        $item   = new \stdClass();

        $result = $this->itemValueReader->read($preset, $item);

        self::assertSame($item, $result);
    }

    public function testReadFromArrayByPresetSource(): void
    {
        $preset = $this->makePreset('name');

        $result = $this->itemValueReader->read($preset, ['name' => 'Alice']);

        self::assertSame('Alice', $result);
    }

    public function testReadFromArrayByExplicitSource(): void
    {
        $preset = $this->makePreset(null);

        $result = $this->itemValueReader->read($preset, ['name' => 'Alice'], 'name');

        self::assertSame('Alice', $result);
    }

    public function testReadFromArrayMissingKeyThrows(): void
    {
        $preset = $this->makePreset('missing');

        $this->expectException(UndefinedArrayKeyException::class);

        $this->itemValueReader->read($preset, ['name' => 'Alice']);
    }

    public function testReadFromObject(): void
    {
        $preset  = $this->makePreset('name');
        $object  = new \stdClass();

        $this->propertyAccessor
            ->method('getValue')
            ->willReturn('Alice');

        $result = $this->itemValueReader->read($preset, $object);

        self::assertSame('Alice', $result);
    }

    public function testReadFromObjectThrows(): void
    {
        $preset = $this->makePreset('name');
        $object = new \stdClass();

        $this->propertyAccessor
            ->method('getValue')
            ->willThrowException(new RuntimeException('not readable'));

        $this->expectException(UnreadableObjectPropertyException::class);

        $this->itemValueReader->read($preset, $object);
    }

    #[DataProvider('provideScalarItems')]
    public function testReadFromScalarItemReturnsItem(mixed $item): void
    {
        $preset = $this->makePreset(null);

        $result = $this->itemValueReader->read($preset, $item);

        self::assertSame($item, $result);
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function provideScalarItems(): array
    {
        return [
            'string'       => ['Alice'],
            'empty string' => [''],
            'integer'      => [42],
            'float'        => [12.5],
            'true'         => [true],
            'false'        => [false],
            'null'         => [null],
        ];
    }

    public function testReadFromScalarItemIgnoresPresetSource(): void
    {
        $preset = $this->makePreset('name');

        $result = $this->itemValueReader->read($preset, 'Alice');

        self::assertSame('Alice', $result);
    }

    public function testReadFromScalarItemWithExplicitSourceThrows(): void
    {
        $preset = $this->makePreset(null);

        $this->expectException(UnexpectedItemSourceException::class);

        $this->itemValueReader->read($preset, 'Alice', 'name');
    }

    #[DataProvider('provideEnumItems')]
    public function testReadFromEnumItemReturnsItem(UnitEnum $item): void
    {
        $preset = $this->makePreset(null);

        $result = $this->itemValueReader->read($preset, $item);

        self::assertSame($item, $result);
    }

    /**
     * @return array<string, array{UnitEnum}>
     */
    public static function provideEnumItems(): array
    {
        return [
            'backed enum' => [StatusEnum::Alive],
            'pure enum'   => [PureEnum::Alive],
        ];
    }

    public function testReadFromEnumItemIgnoresPresetSource(): void
    {
        $preset = $this->makePreset('lifeStatus');

        $result = $this->itemValueReader->read($preset, StatusEnum::Alive);

        self::assertSame(StatusEnum::Alive, $result);
    }

    public function testReadFromEnumItemWithExplicitSourceThrows(): void
    {
        $preset = $this->makePreset(null);

        $this->expectException(UnexpectedItemSourceException::class);

        $this->itemValueReader->read($preset, StatusEnum::Alive, 'lifeStatus');
    }

    public function testReadFromObjectHoldingAnEnumStillUsesPresetSource(): void
    {
        $preset = $this->makePreset('lifeStatus');
        $item   = new \stdClass();

        $this->propertyAccessor
            ->method('getValue')
            ->willReturn(StatusEnum::Alive);

        $result = $this->itemValueReader->read($preset, $item);

        self::assertSame(StatusEnum::Alive, $result);
    }

    /**
     * @param null|non-empty-string $source
     */
    private function makePreset(?string $source): Preset
    {
        return new Preset(
            id:         'test',
            source:     $source,
            template:   null,
            properties: new PresetPropertyCollection([]),
        );
    }
}
