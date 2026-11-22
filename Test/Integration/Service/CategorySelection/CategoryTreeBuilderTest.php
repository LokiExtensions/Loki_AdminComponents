<?php declare(strict_types=1);

namespace Loki\AdminComponents\Test\Integration\Service\CategorySelection;

use Loki\AdminComponents\Data\CategoryTreeNode;
use Loki\AdminComponents\Service\CategorySelection\CategoryTreeBuilder;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Test\Fixture\Category as CategoryFixture;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\App\ObjectManager;
use Magento\TestFramework\Fixture\AppArea;
use Magento\TestFramework\Fixture\DataFixture;
use Magento\TestFramework\Fixture\DataFixtureStorageManager;
use PHPUnit\Framework\TestCase;

#[AppArea('adminhtml')]
class CategoryTreeBuilderTest extends TestCase
{
    protected function setUp(): void
    {
        ObjectManager::getInstance()->get(CacheInterface::class)->clean([Category::CACHE_TAG]);
    }

    #[
        DataFixture(CategoryFixture::class, ['name' => 'LokiTreeParent'], 'parent'),
        DataFixture(CategoryFixture::class, ['name' => 'LokiTreeChild', 'parent_id' => '$parent.id$'], 'child'),
        DataFixture(
            CategoryFixture::class,
            ['name' => 'LokiTreeGrandchild', 'parent_id' => '$child.id$', 'is_active' => false],
            'grandchild'
        ),
    ]
    public function testBuildsNestedTreeFromRealCategories(): void
    {
        $fixtures = DataFixtureStorageManager::getStorage();
        $parentId = (int) $fixtures->get('parent')->getId();
        $childId = (int) $fixtures->get('child')->getId();
        $grandchildId = (int) $fixtures->get('grandchild')->getId();

        $builder = ObjectManager::getInstance()->get(CategoryTreeBuilder::class);
        $tree = $builder->build(0, 'LokiTreeGrandchild');

        $this->assertCount(1, $tree);
        $defaultCategory = $tree[0];
        $this->assertSame(2, $defaultCategory->id);
        $this->assertSame(0, $defaultCategory->level);

        $parent = $this->findChild($defaultCategory, $parentId);
        $this->assertSame('LokiTreeParent', $parent->label);
        $this->assertSame(1, $parent->level);
        $this->assertSame([2], $parent->parentIds);
        $this->assertCount(1, $parent->children);

        $child = $this->findChild($parent, $childId);
        $this->assertSame(2, $child->level);
        $this->assertSame([2, $parentId], $child->parentIds);

        $grandchild = $this->findChild($child, $grandchildId);
        $this->assertSame('LokiTreeGrandchild', $grandchild->label);
        $this->assertFalse($grandchild->isActive);
        $this->assertSame(3, $grandchild->level);
        $this->assertSame($childId, $grandchild->parentId);
        $this->assertSame([2, $parentId, $childId], $grandchild->parentIds);
        $this->assertSame([], $grandchild->children);

        $this->assertSame(3, $builder->calculateMaxLevel($tree));
        $this->assertSame([2, $parentId, $childId, $grandchildId], $builder->buildHoverSequence($tree));
        $this->assertEquals($tree, $builder->build(0, 'LokiTreeGrandchild'));
    }

    #[
        DataFixture(CategoryFixture::class, ['name' => 'LokiTreeUnfilteredParent'], 'parent'),
        DataFixture(CategoryFixture::class, ['name' => 'LokiTreeUnfilteredChild', 'parent_id' => '$parent.id$'], 'child'),
    ]
    public function testUnfilteredTreeContainsAllCategories(): void
    {
        $fixtures = DataFixtureStorageManager::getStorage();
        $parentId = (int) $fixtures->get('parent')->getId();
        $childId = (int) $fixtures->get('child')->getId();

        $tree = ObjectManager::getInstance()->get(CategoryTreeBuilder::class)->build();

        $defaultCategory = $tree[0];
        $parent = $this->findChild($defaultCategory, $parentId);
        $this->assertSame($childId, $this->findChild($parent, $childId)->id);
    }

    private function findChild(CategoryTreeNode $node, int $id): CategoryTreeNode
    {
        foreach ($node->children as $child) {
            if ($child->id === $id) {
                return $child;
            }
        }

        $this->fail('Category ' . $id . ' not found below category ' . $node->id);
    }
}
