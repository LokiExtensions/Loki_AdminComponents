<?php
declare(strict_types=1);

namespace Yireo\LokiAdminComponents\ProviderHandler;

use Magento\Framework\Api\Filter;
use Magento\Framework\Api\FilterFactory;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\DataObject;
use RuntimeException;
use Yireo\LokiAdminComponents\Grid\State as GridState;

class RepositoryHandler implements ProviderHandlerInterface
{
    public function __construct(
        private FilterFactory $filterFactory,
        private FilterGroupBuilder $filterGroupBuilder,
        private SearchCriteriaBuilder $searchCriteriaBuilder,
    ) {
    }

    public function getItems($provider, GridState $gridState): array
    {
        if (false === method_exists($provider, 'getList')) {
            throw new RuntimeException('Repository "getList" is not available.');
        }

        $searchResults = $provider->getList($this->getSearchCriteriaBuilder($gridState)->create());
        $gridState->setTotalItems($searchResults->getTotalCount());

        return $searchResults->getItems();
    }

    public function getItem($provider, int|string $identifier): DataObject
    {
        if (empty($identifier)) {
            throw new RuntimeException('Empty identifier');
        }

        if (method_exists($provider, 'getById')) {
            return call_user_func([$provider, 'getById'], $identifier);
        }

        if (method_exists($provider, 'get')) {
            return call_user_func([$provider, 'get'], $identifier);
        }

        throw new RuntimeException('Repository has no method we know of.');
    }

    public function saveItem($provider, DataObject $item): void
    {
        $provider->save($item);
    }

    public function deleteItem($provider, DataObject $item): void
    {
        $provider->delete($item);
    }

    public function duplicateItem($provider, DataObject $item): void
    {
        $provider->duplicate($item);
    }

    private function getSearchCriteriaBuilder(GridState $gridState): SearchCriteriaBuilder
    {
        $this->searchCriteriaBuilder->setPageSize($gridState->getLimit());
        $this->searchCriteriaBuilder->setCurrentPage($gridState->getPage());
        //$this->searchCriteriaBuilder->setSortOrders([]); @todo: Implement sort ordering

        $filterGroups = $this->getFilterGroups($gridState);
        if (!empty($filterGroups)) {
            $this->searchCriteriaBuilder->setFilterGroups($this->getFilterGroups($gridState));
        }

        return $this->searchCriteriaBuilder;
    }

    private function getFilterGroups(GridState $gridState): array
    {
        $filterGroups = [];

        $searchFilterGroup = $this->getSearchFilterGroup($gridState);
        if ($searchFilterGroup) {
            $filterGroups[] = $searchFilterGroup;
        }

        return $filterGroups;
    }

    private function getSearchFilterGroup(GridState $gridState): ?FilterGroup
    {
        $search = $gridState->getSearch();
        if (empty($search)) {
            return null;
        }

        $filters = [];
        foreach ($gridState->getSearchableFields() as $searchableField) {
            $filter = $this->filterFactory->create();
            $filter->setField($searchableField);
            $filter->setValue('%'.$search.'%');
            $filter->setConditionType('like');
            $filters[] = $filter;
        }

        return $this->filterGroupBuilder->setFilters($filters)->create();
    }
}
