<?php declare(strict_types=1);

namespace Yireo\LokiAdminComponents\Grid\GridDataProvider;

use Magento\Framework\DataObject;
use Magento\Framework\Api\FilterFactory;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\View\Element\AbstractBlock;
use RuntimeException;
use Yireo\LokiAdminComponents\RepositoryProvider\RepositoryProviderInterface;
use Yireo\LokiAdminComponents\Grid\State;

class RepositoryGridDataProvider implements GridDataProviderInterface
{
    private ?AbstractBlock $block = null;

    public function __construct(
        private State $state,
        private FilterFactory $filterFactory,
        private FilterGroupBuilder $filterGroupBuilder,
        private SearchCriteriaBuilder $searchCriteriaBuilder,
        private RepositoryProviderInterface $repositoryProvider,
    ) {
    }

    public function getItems(): array
    {
        $repository = $this->repositoryProvider->getRepository();
        if (false === method_exists($repository, 'getList')) {
            throw new RuntimeException('Repository "getList" is not available.');
        }

        $searchResults = $repository->getList($this->getSearchCriteriaBuilder()->create());
        $this->state->setTotalItems($searchResults->getTotalCount());

        return $searchResults->getItems();
    }

    protected function getSearchCriteriaBuilder(): SearchCriteriaBuilder
    {
        $this->searchCriteriaBuilder->setPageSize($this->state->getLimit());
        $this->searchCriteriaBuilder->setCurrentPage($this->state->getPage());
        //$this->searchCriteriaBuilder->setSortOrders([]); @todo: Implement sort ordering

        $filterGroups = $this->getFilterGroups();
        if (!empty($filterGroups)) {
            $this->searchCriteriaBuilder->setFilterGroups($this->getFilterGroups());
        }

        return $this->searchCriteriaBuilder;
    }

    protected function getFilterGroups(): array
    {
        $filterGroups = [];

        $searchFilterGroup = $this->getSearchFilterGroup();
        if ($searchFilterGroup) {
            $filterGroups[] = $searchFilterGroup;
        }

        return $filterGroups;
    }

    protected function getSearchFilterGroup(): ?FilterGroup
    {
        $search = $this->state->getSearch();
        if (empty($search)) {
            return null;
        }

        $filters = [];
        foreach ($this->getSearchableFields() as $searchableField) {
            $filter = $this->filterFactory->create();
            $filter->setField($searchableField);
            $filter->setValue('%'.$search.'%');
            $filter->setConditionType('like');
            $filters[] = $filter;
        }

        return $this->filterGroupBuilder->setFilters($filters)->create();
    }

    public function getCellActions(DataObject $item): array
    {
        return $this->repositoryProvider->getCellActions($item);
    }

    private function getSearchableFields(): array
    {
        return (array)$this->block->getSearchableFields();
    }

    public function setBlock(AbstractBlock $block): GridDataProviderInterface
    {
        $this->block = $block;

        return $this;
    }
}
