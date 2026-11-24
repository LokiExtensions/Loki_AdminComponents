<?php declare(strict_types=1);

namespace Loki\AdminComponents\Test\Unit\Grid;

use Loki\AdminComponents\Grid\BookmarkLoader;
use Loki\AdminComponents\Grid\State;
use Loki\AdminComponents\Grid\State\FilterStateFactory;
use Magento\Backend\Model\Session;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class StateSortByTest extends TestCase
{
    #[DataProvider('getSortByValues')]
    public function testSortByOnlyReturnsColumnIdentifiers(mixed $storedValue, string $expected): void
    {
        $session = $this->createStub(Session::class);
        $session->method('getData')->willReturn($storedValue);

        $state = new State(
            $session,
            $this->createStub(FilterStateFactory::class),
            $this->createStub(BookmarkLoader::class),
            'ns'
        );

        $this->assertSame($expected, $state->getSortBy());
    }

    public static function getSortByValues(): array
    {
        return [
            'column' => ['entity_type_code', 'entity_type_code'],
            'qualified column' => ['main_table.entity_type_id', 'main_table.entity_type_id'],
            'empty' => ['', ''],
            'null' => [null, ''],
            'subquery' => ['(SELECT SLEEP(3))', ''],
            'function' => ['IF(1=1,entity_type_id,entity_type_code)', ''],
            'comment' => ['entity_type_id-- ', ''],
            'space' => ['entity_type_id DESC', ''],
            'backtick' => ['`entity_type_id`', ''],
            'double dot' => ['a.b.c', ''],
            'newline' => ["entity_type_id\n", ''],
        ];
    }
}
