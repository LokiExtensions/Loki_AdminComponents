<?php declare(strict_types=1);

namespace Loki\AdminComponents\Test\Unit\Grid;

use Loki\AdminComponents\Grid\BookmarkLoader;
use Loki\AdminComponents\Grid\State;
use Loki\AdminComponents\Grid\State\FilterStateFactory;
use Magento\Backend\Model\Session;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Ui\Api\Data\BookmarkInterface;
use PHPUnit\Framework\TestCase;

class StateTest extends TestCase
{
    public function testBookmarkColumnsAreUpdatedAndKeepOtherKeys(): void
    {
        $this->assertSavedColumns(
            ['columns' => [
                'name' => ['visible' => 0, 'sorting' => 'asc'],
                'email' => ['visible' => 1],
            ]],
            ['name'],
            [],
            [
                'name' => ['visible' => 1, 'sorting' => 'asc'],
                'email' => ['visible' => 0],
            ]
        );
    }

    public function testColumnsMissingFromBookmarkCanBeHidden(): void
    {
        $this->assertSavedColumns(
            ['columns' => ['name' => ['visible' => 1]]],
            ['name'],
            ['name', 'email', 'status'],
            [
                'name' => ['visible' => 1],
                'email' => ['visible' => 0],
                'status' => ['visible' => 0],
            ]
        );
    }

    public function testColumnsMissingFromBookmarkCanBeShown(): void
    {
        $this->assertSavedColumns(
            ['columns' => ['name' => ['visible' => 1]]],
            ['name', 'email'],
            ['name', 'email'],
            [
                'name' => ['visible' => 1],
                'email' => ['visible' => 1],
            ]
        );
    }

    public function testBookmarkWithoutColumnsUsesColumnNamesAsKeys(): void
    {
        $this->assertSavedColumns(
            [],
            ['name', 'email'],
            [],
            [
                'name' => ['visible' => 1],
                'email' => ['visible' => 1],
            ]
        );
    }

    public function testBookmarkWithoutColumnsHidesInactiveAvailableColumns(): void
    {
        $this->assertSavedColumns(
            [],
            ['email'],
            ['name', 'email'],
            [
                'name' => ['visible' => 0],
                'email' => ['visible' => 1],
            ]
        );
    }

    public function testMissingBookmarkCreatesBookmarkWithColumnNamesAsKeys(): void
    {
        $bookmarkLoader = $this->createMock(BookmarkLoader::class);
        $bookmarkLoader->method('getBookmarkData')->willThrowException(new NoSuchEntityException());
        $bookmarkLoader->method('getBookmark')->willThrowException(new NoSuchEntityException());
        $bookmarkLoader->expects($this->never())->method('saveBookmark');
        $bookmarkLoader->expects($this->once())->method('createBookmark')->with(
            'ns',
            ['columns' => ['name' => ['visible' => 1], 'email' => ['visible' => 0]]]
        );

        $this->getState($bookmarkLoader)->setActiveColumns(['name'], ['name', 'email']);
    }

    public function testLimitIsClampedToMaximum(): void
    {
        $session = $this->createStub(Session::class);
        $session->method('getData')->willReturn(100000000);

        $state = new State(
            $session,
            $this->createStub(FilterStateFactory::class),
            $this->createStub(BookmarkLoader::class),
            'ns',
            20,
            500
        );

        $this->assertSame(500, $state->getLimit());
    }

    public function testSetLimitIsClampedToMaximum(): void
    {
        $session = $this->createStub(Session::class);

        $bookmarkLoader = $this->createMock(BookmarkLoader::class);
        $bookmarkLoader->method('getBookmark')->willThrowException(new NoSuchEntityException());
        $bookmarkLoader->expects($this->once())->method('createBookmark')
            ->with('ns', ['paging' => ['pageSize' => 1000]]);

        $state = new State($session, $this->createStub(FilterStateFactory::class), $bookmarkLoader, 'ns');
        $state->setLimit(100000000);
    }

    private function assertSavedColumns(
        array $bookmarkData,
        array $activeColumns,
        array $allColumns,
        array $expectedColumns
    ): void {
        $bookmark = $this->createStub(BookmarkInterface::class);

        $bookmarkLoader = $this->createMock(BookmarkLoader::class);
        $bookmarkLoader->method('getBookmarkData')->with('ns')->willReturn($bookmarkData);
        $bookmarkLoader->method('getBookmark')->with('ns')->willReturn($bookmark);
        $bookmarkLoader->expects($this->once())
            ->method('saveBookmark')
            ->with($bookmark, ['columns' => $expectedColumns]);

        $this->getState($bookmarkLoader)->setActiveColumns($activeColumns, $allColumns);
    }

    private function getState(BookmarkLoader $bookmarkLoader): State
    {
        return new State(
            $this->createStub(Session::class),
            $this->createStub(FilterStateFactory::class),
            $bookmarkLoader,
            'ns'
        );
    }
}
