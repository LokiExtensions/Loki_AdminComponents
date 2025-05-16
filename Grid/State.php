<?php
declare(strict_types=1);

namespace Yireo\LokiAdminComponents\Grid;

use Magento\Backend\Model\Session;

class State
{
    public function __construct(
        private Session $session,
        private string $namespace,
        private int $defaultLimit = 20,
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
    }

    public function getSearchableFields(): array
    {
        return explode(',', (string)$this->get('searchable_fields'));
    }

    public function setSearchableFields(array $searchableFields): void
    {
        $this->save('searchable_fields', implode(',', $searchableFields));
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'page' => $this->getPage(),
            'limit' => $this->getLimit(),
            'totalItems' => $this->getTotalItems(),
            'totalPages' => $this->getTotalPages(),
            'searchableFields' => $this->getSearchableFields(),
            'search' => $this->getSearch(),
        ];
    }

    private function get(string $name): mixed
    {
        return $this->session->getData($this->namespace.'.'.$name);
    }

    private function save(string $name, mixed $value)
    {
        $this->session->setData($this->namespace.'.'.$name, $value);
    }
}
