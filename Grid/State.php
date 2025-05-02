<?php
declare(strict_types=1);

namespace Yireo\LokiAdminComponents\Grid;

use Magento\Backend\Model\Session;

class State
{
    private int $page = 1;
    private int $limit = 20;
    private int $totalItems = 0;
    private string $search = '';

    public function __construct(
        private Session $session
    ) {
        $this->page = (int)$session->getData('page');
        if ($this->page < 1) {
            $this->page = 1;
        }

        $limit = (int)$session->getData('limit');
        if ($limit > 0) {
            $this->limit = $limit;
        }

        $this->totalItems = (int)$session->getData('totalItems');
        $this->search = (string)$session->getData('search');
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function setPage(int $page): void
    {
        $this->save('page', $page);
        $this->page = $page;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function setLimit(int $limit): void
    {
        $this->save('limit', $limit);
        $this->limit = $limit;
    }

    public function getTotalItems(): int
    {
        return $this->totalItems;
    }

    public function setTotalItems(int $totalItems): void
    {
        $this->save('totalItems', $totalItems);
        $this->totalItems = $totalItems;
    }

    public function getTotalPages(): int
    {
        return (int)ceil($this->totalItems / $this->limit);
    }

    public function getSearch(): string
    {
        return $this->search;
    }

    public function setSearch(string $search): void
    {
        $this->save('search', $search);
        $this->search = $search;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'page' => $this->page,
            'limit' => $this->limit,
            'totalItems' => $this->totalItems,
            'totalPages' => $this->getTotalPages(),
            'search' => $this->search,
        ];
    }

    private function save(string $name, $value)
    {
        // @todo: Save this under a specific prefix
        $this->session->setData($name, $value);
    }
}
