<?php
declare(strict_types=1);

namespace Loki\AdminComponents\Grid;

use Loki\AdminComponents\Grid\Column\Column;
use Loki\AdminComponents\Grid\State\FilterState;
use Loki\AdminComponents\Grid\State\FilterStateFactory;
use Magento\Backend\Model\Session;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Ui\Api\Data\BookmarkInterface;

class State
{
    public function __construct(
        private readonly Session $session,
        private readonly FilterStateFactory $filterStateFactory,
        private readonly BookmarkLoader $bookmarkLoader,
        private readonly string $namespace,
        private readonly int $defaultLimit = 20,
    ) {
    }

    public function getPage(): int
    {
        $originalPage = (int)$this->get('page');
        $page = $originalPage;

        if ($page < 1) {
            $page = 1;
        }

        if ($page > $this->getTotalPages()) {
            $page = $this->getTotalPages();
        }

        if ($page < 1) {
            $page = 1;
        }

        if ($page !== $originalPage) {
            $this->save('page', $page);
        }

        return $page;
    }

    public function setPage(int $page): void
    {
        $this->save('page', $page);
        $this->saveBookmarkData(['paging' => ['current' => $page]]);
    }

    public function getLimit(): int
    {
        $originalLimit = (int)$this->get('limit');
        $limit = $originalLimit;

        if ($limit < 1) {
            $limit = $this->defaultLimit;
        }

        return $limit;
    }

    public function setLimit(int $limit): void
    {
        $this->save('limit', $limit);
        $this->saveBookmarkData(['paging' => ['pageSize' => $limit]]);
    }

    public function getSortBy(): string
    {
        return (string)$this->get('sort_by');
    }

    public function setSortBy(string $sortBy): void
    {
        $this->save('sort_by', $sortBy);
    }

    public function getSortDirection(): string
    {
        $sortDirection = strtoupper((string)$this->get('sort_direction'));
        if (($sortDirection === AbstractDb::SORT_ORDER_ASC)) {
            return AbstractDb::SORT_ORDER_ASC;
        }

        return AbstractDb::SORT_ORDER_DESC;
    }

    public function setSortDirection(string $sortDirection): void
    {
        if ($sortDirection === AbstractDb::SORT_ORDER_ASC) {
            $this->save('sort_direction', $sortDirection);

            return;
        }

        $this->save('sort_direction', AbstractDb::SORT_ORDER_DESC);
    }

    public function getTotalItems(): int
    {
        return (int)$this->get('total_items');
    }

    public function setTotalItems(int $totalItems): void
    {
        $this->save('total_items', $totalItems);
    }

    public function getTotalPages(): int
    {
        return (int)ceil($this->getTotalItems() / $this->getLimit());
    }

    public function getSearch(): string
    {
        return (string)$this->get('search');
    }

    public function setSearch(string $search): void
    {
        $this->save('search', $search);
        $this->saveBookmarkData(['search' => ['value' => $search]]);
    }

    public function getSearchableFields(): array
    {
        return explode(',', (string)$this->get('searchable_fields'));
    }

    /**
     * @param array $searchableFields
     *
     * @return Column[]
     */
    public function setSearchableFields(array $searchableFields): void
    {
        $this->save('searchable_fields', implode(',', $searchableFields));
    }

    /**
     * @return FilterState[]
     * @todo Rename this getFilterStates()
     */
    public function getFilters(): array
    {
        $filters = [];
        $filtersData = json_decode((string)$this->get('filters'), true);
        if (false === is_array($filtersData)) {
            return [];
        }

        foreach ($filtersData as $filterData) {
            if (!isset($filterData['value'])
                || !isset($filterData['field'])
                || !isset($filterData['condition_type'])) {
                continue;
            }

            $filters[$filterData['field']] = $this->filterStateFactory->create(
                $filterData['field'],
                $filterData['value'],
                $filterData['condition_type'],
            );
        }

        return $filters;
    }

    public function getFilterState(string $name): ?FilterState
    {
        $filters = $this->getFilters();
        if (isset($filters[$name])) {
            return $filters[$name];
        }

        return null;
    }

    public function setFilter(string $name, mixed $value, ?string $conditionType = 'like'): void
    {
        $filters = $this->getFilters();

        if (isset($filters[$name])) {
            unset($filters[$name]);
        }

        $filters[$name] = [
            'field' => $name,
            'value' => $value,
            'condition_type' => $conditionType,
        ];

        $this->setFilters($filters);
        $this->saveBookmarkData(['filters' => ['applied' => [$name => $value]]]);
    }

    /**
     * @param mixed[] $filters
     *
     * @return void
     */
    public function setFilters(array $filters): void
    {
        $this->save('filters', json_encode($filters));
    }

    public function getActiveColumns(): array
    {
        $bookmarkData = $this->getBookmarkData();
        if (false === is_array($bookmarkData['columns'])) {
            return [];
        }

        $columns = [];
        foreach ($bookmarkData['columns'] as $columnName => $columnData) {
            if (isset($columnData['visible']) && (int)$columnData['visible'] === 1) {
                $columns[] = $columnName;
            }
        }

        return $columns;
    }

    public function setActiveColumns(array $columns): void
    {
        $bookmarkData = $this->getBookmarkData();
        $columnsData = [];

        if (isset($bookmarkData['columns'])) {
            foreach ($bookmarkData['columns'] as $columnName => $columnData) {
                if (!is_array($columnData)) {
                    $columnData = [];
                }

                $columnData['visible'] =  (int)in_array($columnName, $columns);
                $columnsData[$columnName] = $columnData;
            }
        }

        $this->saveBookmarkData(['columns' => $columnsData]);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'page' => $this->getPage(),
            'limit' => $this->getLimit(),
            'sortBy' => $this->getSortBy(),
            'sortDirection' => $this->getSortDirection(),
            'totalItems' => $this->getTotalItems(),
            'totalPages' => $this->getTotalPages(),
            'searchableFields' => $this->getSearchableFields(),
            'search' => $this->getSearch(),
            'filters' => $this->getFilters(),
            'activeColumns' => $this->getActiveColumns(),
        ];
    }

    public function get(string $name): mixed
    {
        return $this->session->getData($this->namespace.'.'.$name);
    }

    public function save(string $name, mixed $value): void
    {
        $this->session->setData($this->namespace.'.'.$name, $value);
    }

    public function saveBookmarkData(array $bookmarkData): void
    {
        try {
            $this->bookmarkLoader->saveBookmark($this->getBookmark(), $bookmarkData);
        } catch (NoSuchEntityException $e) {
        }
    }

    public function reset(): void
    {
        $this->setFilters([]);
        $this->setSearch('');
    }

    private function getBookmark(): BookmarkInterface
    {
        return $this->bookmarkLoader->getBookmark($this->namespace);
    }

    private function getBookmarkData(): array
    {
        return $this->bookmarkLoader->getBookmarkData($this->namespace);
    }
}
