<?php declare(strict_types=1);

namespace Loki\AdminComponents\Test\Unit\ProviderHandler;

use Loki\AdminComponents\Grid\State as GridState;
use Loki\AdminComponents\ProviderHandler\CollectionHandler;
use Loki\AdminComponents\Util\AbstractEntityColumnLoader;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DB\Helper as DbHelper;
use Magento\Framework\ObjectManagerInterface;
use PHPUnit\Framework\TestCase;

class CollectionHandlerSearchTest extends TestCase
{
    public function testSearchWildcardsAreEscaped(): void
    {
        $gridState = $this->createStub(GridState::class);
        $gridState->method('getLimit')->willReturn(20);
        $gridState->method('getPage')->willReturn(1);
        $gridState->method('getSearch')->willReturn('50%_off');
        $gridState->method('getSearchableFields')->willReturn(['name']);
        $gridState->method('getFilters')->willReturn([]);

        $collection = $this->createMock(AbstractDb::class);
        $collection->expects($this->once())
            ->method('addFieldToFilter')
            ->with(['name'], [['like' => '%50\%\_off%']]);
        $collection->method('getSize')->willReturn(0);
        $collection->method('getItems')->willReturn([]);

        $handler = new CollectionHandler(
            $this->createStub(ObjectManagerInterface::class),
            $this->createStub(AbstractEntityColumnLoader::class),
            $this->createPartialMock(DbHelper::class, [])
        );

        $this->assertSame([], $handler->getItems($collection, $gridState));
    }
}
