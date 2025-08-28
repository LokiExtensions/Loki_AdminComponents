<?php
declare(strict_types=1);

namespace Loki\AdminComponents\ProviderHandler;

use Magento\Framework\Api\FilterFactory;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SortOrderFactory;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DataObject;
use RuntimeException;
use Loki\AdminComponents\Grid\State as GridState;

class RepositoryHandler implements ProviderHandlerInterface
{
    public function __construct(
        private FilterFactory $filterFactory,
        private FilterGroupBuilder $filterGroupBuilder,
        private SearchCriteriaBuilder $searchCriteriaBuilder,
        private SortOrderFactory $sortOrderFactory
    ) {
    }

    public function match(object $provider): bool
    {
        return str_ends_with(get_class($provider), 'Repository')
            || str_ends_with(get_class($provider), 'Repository\Interceptor');
    }

    public function getItems(object $provider, GridState $gridState): array
    {
        if (false === method_exists($provider, 'getList')) {
            throw new RuntimeException('Repository "getList" is not available.');
        }

        $searchResults = $provider->getList($this->getSearchCriteriaBuilder($gridState)->create());
        $gridState->setTotalItems($searchResults->getTotalCount());

        return $searchResults->getItems();
    }

    public function getItem(object $provider, int|string $identifier): DataObject
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

        throw new RuntimeException('Repository has no load-method we know of.');
    }

    public function createItem(object $provider, object|null $factory): DataObject
    {
        if (method_exists($provider, 'create')) {
            return call_user_func([$provider, 'create']);
        }

        if (is_object($factory) && method_exists($factory, 'create')) {
            return call_user_func([$factory, 'create']);
        }

        throw new RuntimeException('Repository has no create-method we know of.');
    }

    public function saveItem(object $provider, DataObject $item): void
    {
        $provider->save($item);
    }

    public function deleteItem(object $provider, DataObject $item): void
    {
        $provider->delete($item);
    }

    public function duplicateItem(object $provider, DataObject $item): void
    {
        $provider->duplicate($item);
    }

    public function getColumns(object $provider): array
    {
        return [];
    }

    private function getSearchCriteriaBuilder(GridState $gridState): SearchCriteriaBuilder
    {
        $this->searchCriteriaBuilder->setPageSize($gridState->getLimit());
        $this->searchCriteriaBuilder->setCurrentPage($gridState->getPage());

        $sortField = $gridState->getSortBy();
        $sortDirection = $gridState->getSortDirection();
        if (!empty($sortField)) {
            /** @var SortOrder $sortOrder */
            $sortOrder = $this->sortOrderFactory->create([]);
            $sortOrder->setField($sortField);
            $sortOrder->setDirection($sortDirection);
            $this->searchCriteriaBuilder->setSortOrders([$sortOrder]);
        }

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

        foreach ($gridState->getFilters() as $filterData) {
            $filter = $this->filterFactory->create();
            $filter->setField($filterData['field']);
            $filter->setValue($filterData['value']);
            $filter->setConditionType($filterData['condition_type']);
            $filterGroups[] = $this->filterGroupBuilder->setFilters([$filter])->create();
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

    public function getModelClass(object $provider): bool|string
    {
        $providerReflection = new \ReflectionClass($provider);
        $methodReflection = $providerReflection->getMethod('save');
        $returnTypes = $methodReflection->getReturnType()->getTypes();
        $returnType = array_pop($returnTypes);
        return $returnType;
    }

    public function getResourceModelClass(object $provider): bool|string
    {
        return false;
    }
}
