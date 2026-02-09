<?php

declare(strict_types=1);

namespace Loki\AdminComponents\Service\CategorySelection;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\ResourceModel\Category\Collection;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\DB\Helper as DbHelper;

class CategoryFilterService
{
    public function __construct(
        private readonly CollectionFactory $categoryCollectionFactory,
        private readonly DbHelper $dbHelper,
    ) {
    }

    /**
     * @return int[]
     */
    public function getVisibleCategoryIds(int $storeId, ?string $filter): array
    {
        $collection = $this->createFilteredCollection($storeId, $filter);

        return $this->extractCategoryIdsWithAncestors($collection);
    }

    private function createFilteredCollection(int $storeId, ?string $filter): Collection
    {
        $collection = $this->categoryCollectionFactory->create();
        $collection->addAttributeToSelect('path')
            ->addAttributeToFilter('entity_id', ['neq' => Category::TREE_ROOT_ID])
            ->setStoreId($storeId);

        if (!empty($filter)) {
            $collection->addAttributeToFilter(
                'name',
                ['like' => $this->dbHelper->addLikeEscape($filter, ['position' => 'any'])]
            );
        }

        return $collection;
    }

    /**
     * @return int[]
     */
    private function extractCategoryIdsWithAncestors(Collection $collection): array
    {
        $categoryIds = [];

        foreach ($collection as $category) {
            $path = $category->getPath() ?? '';
            foreach (explode('/', $path) as $ancestorId) {
                $categoryIds[(int) $ancestorId] = true;
            }
        }

        return array_keys($categoryIds);
    }
}
