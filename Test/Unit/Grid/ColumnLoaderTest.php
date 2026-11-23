<?php declare(strict_types=1);

namespace Loki\AdminComponents\Test\Unit\Grid;

use Loki\AdminComponents\Grid\BookmarkLoader;
use Loki\AdminComponents\Grid\Column\Column;
use Loki\AdminComponents\Grid\Column\ColumnFactory;
use Loki\AdminComponents\Grid\ColumnLoader;
use Loki\AdminComponents\Grid\State;
use Loki\AdminComponents\Grid\StateManager;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Ui\Api\Data\BookmarkInterface;
use PHPUnit\Framework\TestCase;

class ColumnLoaderTest extends TestCase
{
    public function testMissingBookmarkReturnsNoColumns(): void
    {
        $bookmarkLoader = $this->createStub(BookmarkLoader::class);
        $bookmarkLoader->method('getBookmark')->willThrowException(new NoSuchEntityException());

        $columnLoader = new ColumnLoader(
            $this->getColumnFactory(),
            $bookmarkLoader,
            $this->createStub(StateManager::class)
        );

        $this->assertSame([], $columnLoader->getColumnsFromNamespace('ns'));
    }

    public function testBookmarkWithoutColumnsReturnsNoColumns(): void
    {
        $this->assertSame([], $this->getColumnLoader(['positions' => ['name' => 1]])->getColumnsFromNamespace('ns'));
    }

    public function testActionsColumnIsSkipped(): void
    {
        $columns = $this->getColumnLoader([
            'columns' => [
                'actions' => ['visible' => true],
                'name' => ['visible' => true],
            ],
        ])->getColumnsFromNamespace('ns');

        $this->assertSame(['name'], array_map(fn (Column $column) => $column->getCode(), $columns));
    }

    public function testVisibleAndPositionAreOnlySetWhenPresentInBookmark(): void
    {
        $columns = $this->getColumnLoader([
            'columns' => [
                'name' => ['visible' => false],
                'email' => [],
            ],
            'positions' => [
                'email' => 3,
            ],
        ])->getColumnsFromNamespace('ns');

        $this->assertCount(2, $columns);
        [$name, $email] = $columns;

        $this->assertSame('name', $name->getCode());
        $this->assertTrue($name->hasData('visible'));
        $this->assertFalse($name->isVisible());
        $this->assertFalse($name->hasData('position'));

        $this->assertSame('email', $email->getCode());
        $this->assertFalse($email->hasData('visible'));
        $this->assertTrue($email->hasData('position'));
        $this->assertSame(3, $email->getPosition());
    }

    public function testExtraBookmarkKeysAreIgnored(): void
    {
        $columns = $this->getColumnLoader([
            'columns' => [
                'name' => [
                    'visible' => true,
                    'label' => 'Bookmark label',
                    'cell_template' => 'Vendor_Module::cell.phtml',
                    'sorting' => 'asc',
                ],
            ],
            'positions' => [
                'name' => 2,
            ],
        ])->getColumnsFromNamespace('ns');

        $this->assertCount(1, $columns);
        $this->assertSame(
            ['code' => 'name', 'visible' => true, 'position' => 2],
            $columns[0]->getData()
        );
    }

    public function testPagingIsAppliedToGridState(): void
    {
        $state = $this->createMock(State::class);
        $state->expects($this->once())->method('setLimit')->with(50);
        $state->expects($this->once())->method('setPage')->with(2);

        $stateManager = $this->createStub(StateManager::class);
        $stateManager->method('get')->willReturn($state);

        $columnLoader = $this->getColumnLoader(
            ['paging' => ['pageSize' => '50', 'current' => '2']],
            $stateManager
        );

        $this->assertSame([], $columnLoader->getColumnsFromNamespace('ns'));
    }

    public function testUnpositionedColumnsSortAfterPositionedColumns(): void
    {
        $columns = $this->getColumnLoader([])->sortColumns([
            new Column(['code' => 'a']),
            new Column(['code' => 'b', 'position' => 2]),
            new Column(['code' => 'c']),
            new Column(['code' => 'd', 'position' => 0]),
            new Column(['code' => 'e', 'position' => null]),
        ]);

        $this->assertSame(['d', 'b', 'a', 'c', 'e'], $this->getCodes($columns));
    }

    public function testRepositionOfSingleColumnKeepsOthersAfterIt(): void
    {
        $columnLoader = $this->getColumnLoader([
            'columns' => ['name' => ['visible' => 1], 'email' => ['visible' => 1], 'status' => ['visible' => 1]],
            'positions' => ['status' => 0],
        ]);

        $columns = $columnLoader->sortColumns($columnLoader->getColumnsFromNamespace('ns'));

        $this->assertSame(['status', 'name', 'email'], $this->getCodes($columns));
    }

    public function testColumnsWithoutPositionsKeepOriginalOrder(): void
    {
        $columns = $this->getColumnLoader([])->sortColumns([
            new Column(['code' => 'c']),
            new Column(['code' => 'a']),
            new Column(['code' => 'b']),
        ]);

        $this->assertSame(['c', 'a', 'b'], $this->getCodes($columns));
    }

    public function testColumnsWithEqualPositionsKeepOriginalOrder(): void
    {
        $columns = $this->getColumnLoader([])->sortColumns([
            new Column(['code' => 'c', 'position' => 1]),
            new Column(['code' => 'a', 'position' => 0]),
            new Column(['code' => 'b', 'position' => 1]),
        ]);

        $this->assertSame(['a', 'c', 'b'], $this->getCodes($columns));
    }

    private function getCodes(array $columns): array
    {
        return array_map(fn (Column $column) => $column->getCode(), $columns);
    }

    private function getColumnLoader(array $data, ?StateManager $stateManager = null): ColumnLoader
    {
        $bookmark = $this->createStub(BookmarkInterface::class);
        $bookmark->method('getIdentifier')->willReturn('current');
        $bookmark->method('getConfig')->willReturn([
            'views' => [
                'current' => ['data' => $data],
            ],
        ]);

        $bookmarkLoader = $this->createStub(BookmarkLoader::class);
        $bookmarkLoader->method('getBookmark')->willReturn($bookmark);

        return new ColumnLoader(
            $this->getColumnFactory(),
            $bookmarkLoader,
            $stateManager ?? $this->createStub(StateManager::class)
        );
    }

    private function getColumnFactory(): ColumnFactory
    {
        $columnFactory = $this->createStub(ColumnFactory::class);
        $columnFactory->method('create')->willReturnCallback(function (array $data): Column {
            if (!isset($data['visible'])) {
                $data['visible'] = true;
            }

            return new Column($data);
        });

        return $columnFactory;
    }
}
