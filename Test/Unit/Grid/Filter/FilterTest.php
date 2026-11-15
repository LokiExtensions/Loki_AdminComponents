<?php declare(strict_types=1);

namespace Loki\AdminComponents\Test\Unit\Grid\Filter;

use Loki\AdminComponents\Form\Field\FieldFactory;
use Loki\AdminComponents\Grid\Filter\Filter;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Magento\Framework\View\LayoutInterface;

class FilterTest extends TestCase
{
    #[DataProvider('emptyValues')]
    public function testIsEmptyForEmptyValues(string $conditionType, mixed $value): void
    {
        $this->assertTrue($this->getFilter($conditionType)->isEmpty($value));
    }

    public static function emptyValues(): array
    {
        return [
            'null' => ['eq', null],
            'empty string' => ['eq', ''],
            'empty array' => ['in', []],
            'empty from_to' => ['from_to', ['from' => '', 'to' => '']],
        ];
    }

    #[DataProvider('nonEmptyValues')]
    public function testIsEmptyForRealValues(string $conditionType, mixed $value): void
    {
        $this->assertFalse($this->getFilter($conditionType)->isEmpty($value));
    }

    public static function nonEmptyValues(): array
    {
        return [
            'string zero' => ['eq', '0'],
            'int zero' => ['eq', 0],
            'text' => ['like', 'foo'],
            'selection' => ['in', ['1', '2']],
            'selection with zero' => ['in', ['0']],
            'from only' => ['from_to', ['from' => '5', 'to' => '']],
        ];
    }

    private function getFilter(string $conditionType): Filter
    {
        return new Filter(
            $this->createMock(LayoutInterface::class),
            $this->createMock(FieldFactory::class),
            'Label',
            'code',
            $conditionType,
        );
    }
}
