<?php declare(strict_types=1);

namespace Loki\AdminComponents\Test\Unit\Service\CategorySelection;

use Loki\AdminComponents\Data\CategoryTreeNode;
use Loki\AdminComponents\Service\CategorySelection\CategoryTreeBuilder;
use Loki\AdminComponents\Service\CategorySelection\CategoryFilterService;
use Loki\AdminComponents\Model\CategorySelection\CategoryTreeCacheKeyGenerator;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\ResourceModel\Category\Collection;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\Serialize\Serializer\Json;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class CategoryTreeBuilderTest extends TestCase
{
    public function testPreservesSiblingOrderAndAncestorMetadata(): void
    {
        $categories = [
            7 => $this->category(7, 2),
            5 => $this->category(5, 1),
            9 => $this->category(9, 7, false),
            2 => $this->category(2, 1),
            6 => $this->category(6, 2, false),
            8 => $this->category(8, 99),
            1 => $this->category(1, 0),
        ];
        $expected = [
            new CategoryTreeNode(5, 'Category 5', true, 0, 1),
            new CategoryTreeNode(2, 'Category 2', true, 0, 1, [], [
                new CategoryTreeNode(7, 'Category 7', true, 1, 2, [2], [
                    new CategoryTreeNode(9, 'Category 9', false, 2, 7, [2, 7]),
                ]),
                new CategoryTreeNode(6, 'Category 6', false, 1, 2, [2]),
            ]),
        ];

        $this->assertSame(json_encode($expected), json_encode($this->buildTree($categories)));
    }

    public function testEmptyAndDisconnectedCategoriesProduceNoRoots(): void
    {
        $this->assertSame([], $this->buildTree([]));
        $this->assertSame([], $this->buildTree([
            $this->category(3, 99),
            $this->category(4, 3),
        ]));
    }

    public static function categoryCounts(): array
    {
        return [[1000], [5000]];
    }

    #[DataProvider('categoryCounts')]
    public function testLargeTreesPreserveSerializedOutput(int $count): void
    {
        $categories = [$this->category(2, 1)];
        $children = [];
        for ($id = $count + 1; $id >= 3; $id--) {
            $categories[] = $this->category($id, 2);
            $children[] = new CategoryTreeNode($id, 'Category ' . $id, true, 1, 2, [2]);
        }

        $expected = [new CategoryTreeNode(2, 'Category 2', true, 0, 1, [], $children)];
        $this->assertSame(json_encode($expected), json_encode($this->buildTree($categories)));
    }

    private function category(int $id, int $parentId, bool $isActive = true): array
    {
        return ['id' => $id, 'label' => 'Category ' . $id, 'is_active' => $isActive, 'parent_id' => $parentId];
    }

    private function buildTree(array $categories): array
    {
        $items = [];
        foreach ($categories as $category) {
            $item = $this->getMockBuilder(Category::class)
                ->disableOriginalConstructor()
                ->onlyMethods(['getId', 'getName', 'getData'])
                ->getMock();
            $item->method('getId')->willReturn((string) $category['id']);
            $item->method('getName')->willReturn($category['label']);
            $item->method('getData')->willReturnMap([
                ['is_active', null, $category['is_active']],
                ['parent_id', null, (string) $category['parent_id']],
            ]);
            $items[] = $item;
        }

        $categoryIds = array_column($categories, 'id');
        $collection = $this->createMock(Collection::class);
        $collection->method('addAttributeToSelect')->with(['name', 'is_active', 'parent_id'])->willReturnSelf();
        $collection->method('addAttributeToFilter')->with('entity_id', ['in' => $categoryIds])->willReturnSelf();
        $collection->method('setStoreId')->with(0)->willReturnSelf();
        $collection->method('getIterator')->willReturn(new \ArrayIterator($items));

        $factory = $this->createMock(CollectionFactory::class);
        $factory->method('create')->willReturn($collection);
        $filter = $this->createMock(CategoryFilterService::class);
        $filter->expects($this->once())->method('getVisibleCategoryIds')->with(0, null)->willReturn($categoryIds);
        $cache = $this->createMock(CacheInterface::class);
        $cache->expects($this->once())->method('load')->with('category_tree_test')->willReturn(false);
        $cache->expects($this->once())->method('save');
        $cacheKeyGenerator = $this->createMock(CategoryTreeCacheKeyGenerator::class);
        $cacheKeyGenerator->method('generate')->with(0, null)->willReturn('category_tree_test');
        $builder = new CategoryTreeBuilder($factory, $filter, $cache, new Json(), $cacheKeyGenerator);

        return $builder->build();
    }
}
