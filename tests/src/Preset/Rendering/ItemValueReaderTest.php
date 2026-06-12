<?php

declare(strict_types=1);

namespace Jmf\RenderingPreset\Tests\Preset\Rendering;

use Jmf\RenderingPreset\Exception\UndefinedArrayKeyException;
use Jmf\RenderingPreset\Exception\UnreadableObjectPropertyException;
use Jmf\RenderingPreset\Preset\Preset;
use Jmf\RenderingPreset\Preset\Property\PresetPropertyCollection;
use Jmf\RenderingPreset\Preset\Rendering\ItemValueReader;
use Override;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

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

    public function testReadReturnsNullWhenNoSource(): void
    {
        $preset = $this->makePreset(null);

        $result = $this->itemValueReader->read($preset, ['name' => 'Alice']);

        self::assertNull($result);
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
