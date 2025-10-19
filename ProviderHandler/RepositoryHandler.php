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
use Magento\Framework\Model\ResourceModel\Db\AbstractDb as AbstractResourceModel;
use Magento\Framework\DataObject;
use Magento\Framework\ObjectManagerInterface;
use ReflectionParameter;
use RuntimeException;
use Loki\AdminComponents\Grid\State as GridState;

class RepositoryHandler implements ProviderHandlerInterface
{
    public function __construct(
        private FilterFactory $filterFactory,
        private FilterGroupBuilder $filterGroupBuilder,
        private SearchCriteriaBuilder $searchCriteriaBuilder,
        private SortOrderFactory $sortOrderFactory,
        private ObjectManagerInterface $objectManager
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

        $modelClass = $this->getModelClass($provider);
        if (!empty($modelClass)) {
            return $this->objectManager->create($modelClass);
        }

        throw new RuntimeException('Repository has no create-method we know of.');
    }

    public function saveItem(object $provider, DataObject $item): void
    {
        if (method_exists($provider, 'save')) {
            call_user_func([$provider, 'save'], $item);

            return;
        }

        throw new RuntimeException('Repository has no save-method we know of.');
    }

    public function deleteItem(object $provider, DataObject $item): void
    {
        if (method_exists($provider, 'delete')) {
            call_user_func([$provider, 'delete'], $item);

            return;
        }

        throw new RuntimeException('Repository has no delete-method we know of.');
    }

    public function duplicateItem(object $provider, DataObject $item): void
    {
        if (method_exists($provider, 'duplicate')) {
            call_user_func([$provider, 'duplicate'], $item);

            return;
        }

        throw new RuntimeException('Repository has no duplicate-method we know of.');
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

        foreach ($gridState->getFilters() as $gridFilter) {
            $filter = $this->filterFactory->create();
            $filter->setField($gridFilter->getField());

            $value = $gridFilter->getValue();
            $conditionType = $gridFilter->getConditionType();

            if ($conditionType === 'like') {
                $filter->setValue('%'.$value.'%');
                $filter->setConditionType('like');
            } else {
                $filter->setValue($value);
                $filter->setConditionType($conditionType);
            }

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

        /** @var ReflectionParameter $parameter */
        $parameters = $methodReflection->getParameters();
        if (empty($parameters)) {
            return false;
        }

        $parameter = array_shift($parameters);
        return $parameter->getType()->getName();
    }

    public function getResourceModelClass(object $provider): bool|string
    {
        return false;
    }

    public function getResourceModel(object $provider): ?AbstractResourceModel
    {
        return $this->objectManager->get($this->getResourceModelClass($provider));
    }
}
