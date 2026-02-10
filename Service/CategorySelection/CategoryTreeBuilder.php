<?php

declare(strict_types=1);

namespace Loki\AdminComponents\Service\CategorySelection;

use Loki\AdminComponents\Data\CategoryTreeNode;
use Loki\AdminComponents\Model\CategorySelection\CategoryTreeCacheKeyGenerator;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\ResourceModel\Category\Collection;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\App\Cache\Type\Block;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\Serialize\SerializerInterface;

class CategoryTreeBuilder
{
    public function __construct(
        private readonly CollectionFactory $categoryCollectionFactory,
        private readonly CategoryFilterService $categoryFilterService,
        private readonly CacheInterface $cache,
        private readonly SerializerInterface $serializer,
        private readonly CategoryTreeCacheKeyGenerator $cacheKeyGenerator,
    ) {
    }

    /**
     * @return CategoryTreeNode[]
     */
    public function build(int $storeId = 0, ?string $filter = null): array
    {
        $cacheKey = $this->cacheKeyGenerator->generate($storeId, $filter);
        $cachedData = $this->cache->load($cacheKey);

        if ($cachedData !== false) {
            return $this->deserializeCategoryTree($cachedData);
        }

        $tree = $this->buildFreshTree($storeId, $filter);

        $this->cacheTree($cacheKey, $tree);

        return $tree;
    }

    /**
     * @param CategoryTreeNode[] $treeNodes
     */
    public function calculateMaxLevel(array $treeNodes): int
    {
        $maxLevel = 0;

        foreach ($treeNodes as $node) {
            $maxLevel = max($maxLevel, $node->level);

            if ($node->children !== []) {
                $maxLevel = max($maxLevel, $this->calculateMaxLevel($node->children));
            }
        }

        return $maxLevel;
    }

    /**
     * @param CategoryTreeNode[] $treeNodes
     * @return int[]
     */
    public function buildHoverSequence(array $treeNodes): array
    {
        $sequence = [];

        foreach ($treeNodes as $node) {
            $sequence[] = $node->id;
            $sequence = [
                ...$sequence,
                ...$this->buildHoverSequence($node->children)
            ];
        }

        return $sequence;
    }

    /**
     * @return CategoryTreeNode[]
     */
    private function buildFreshTree(int $storeId, ?string $filter): array
    {
        $visibleCategoryIds = $this->categoryFilterService->getVisibleCategoryIds($storeId, $filter);

        if ($visibleCategoryIds === []) {
            return [];
        }

        $flatCategories = $this->loadFlatCategories($storeId, $visibleCategoryIds);

        return $this->buildTreeStructure($flatCategories);
    }

    /**
     * @param int[] $categoryIds
     * @return array<int, array{id: int, label: string, is_active: bool, parent_id: int}>
     */
    private function loadFlatCategories(int $storeId, array $categoryIds): array
    {
        $collection = $this->categoryCollectionFactory->create();
        $collection->addAttributeToSelect(['name', 'is_active', 'parent_id'])
            ->addAttributeToFilter('entity_id', ['in' => $categoryIds])
            ->setStoreId($storeId);

        return $this->convertCollectionToFlatArray($collection);
    }

    /**
     * @return array<int, array{id: int, label: string, is_active: bool, parent_id: int}>
     */
    private function convertCollectionToFlatArray(Collection $collection): array
    {
        $flat = [];

        foreach ($collection as $category) {
            $flat[(int) $category->getId()] = [
                'id' => (int) $category->getId(),
                'label' => (string) $category->getName(),
                'is_active' => (bool) $category->getIsActive(),
                'parent_id' => (int) $category->getParentId(),
            ];
        }

        return $flat;
    }

    /**
     * @param array<int, array{id: int, label: string, is_active: bool, parent_id: int}> $flatCategories
     * @return CategoryTreeNode[]
     */
    private function buildTreeStructure(array $flatCategories): array
    {
        return $this->buildTreeRecursive(
            $flatCategories,
            Category::TREE_ROOT_ID,
            0,
            []
        );
    }

    /**
     * @param array<int, array{id: int, label: string, is_active: bool, parent_id: int}> $flatCategories
     * @param int[] $ancestorIds
     * @return CategoryTreeNode[]
     */
    private function buildTreeRecursive(
        array $flatCategories,
        int $parentId,
        int $level,
        array $ancestorIds
    ): array {
        $nodes = [];

        foreach ($flatCategories as $category) {
            if ($category['parent_id'] !== $parentId) {
                continue;
            }

            $currentAncestorIds = $this->buildAncestorIds($ancestorIds, $parentId);

            $children = $this->buildTreeRecursive(
                $flatCategories,
                $category['id'],
                $level + 1,
                $currentAncestorIds
            );

            $nodes[] = new CategoryTreeNode(
                id: $category['id'],
                label: $category['label'],
                isActive: $category['is_active'],
                level: $level,
                parentId: $parentId,
                parentIds: $currentAncestorIds,
                children: $children
            );
        }

        return $nodes;
    }

    /**
     * @param int[] $ancestorIds
     * @return int[]
     */
    private function buildAncestorIds(array $ancestorIds, int $parentId): array
    {
        if ($parentId === Category::TREE_ROOT_ID) {
            return $ancestorIds;
        }

        return [...$ancestorIds, $parentId];
    }

    /**
     * @param CategoryTreeNode[] $tree
     */
    private function cacheTree(string $cacheKey, array $tree): void
    {
        $this->cache->save(
            $this->serializer->serialize($tree),
            $cacheKey,
            [Category::CACHE_TAG, Block::CACHE_TAG]
        );
    }

    /**
     * @return CategoryTreeNode[]
     */
    private function deserializeCategoryTree(string $cachedData): array
    {
        $treeData = $this->serializer->unserialize($cachedData);

        return $this->convertArrayToTreeNodes($treeData);
    }

    /**
     * @return CategoryTreeNode[]
     */
    private function convertArrayToTreeNodes(array $treeData): array
    {
        return array_map(fn (array $nodeData) =>
            new CategoryTreeNode(
                id: $nodeData['id'],
                label: $nodeData['label'],
                isActive: $nodeData['isActive'],
                level: $nodeData['level'],
                parentId: $nodeData['parentId'],
                parentIds: $nodeData['parentIds'],
                children: $this->convertArrayToTreeNodes($nodeData['children'] ?? [])
            ),
            $treeData
        );
    }
}
