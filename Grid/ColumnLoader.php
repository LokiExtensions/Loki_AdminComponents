<?php declare(strict_types=1);

namespace Loki\AdminComponents\Grid;

use Loki\AdminComponents\Grid\Column\Column;
use Loki\AdminComponents\Grid\Column\ColumnFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\AbstractBlock;

class ColumnLoader
{
    public function __construct(
        private readonly ColumnFactory $columnFactory,
        private readonly BookmarkLoader $bookmarkLoader,
        private readonly StateManager $stateManager
    ) {
    }

    /**
     * @param AbstractBlock $block
     *
     * @return Column[]
     */
    public function getColumnsFromBlock(AbstractBlock $block): array
    {
        $columnDefinitions = $block->getColumns();
        if (false === is_array($columnDefinitions)) {
            return [];
        }

        $columns = [];
        foreach ($columnDefinitions as $columnCode => $columnDefinition) {
            $columnDefinition['code'] = $columnCode;
            $columns[] = $this->columnFactory->create($columnDefinition);
        }

        return $columns;
    }

    /**
     * @param string $namespace
     *
     * @return Column[]
     */
    public function getColumns(string $namespace): array
    {
        try {
            $bookmark = $this->bookmarkLoader->getBookmark($namespace);
            $bookmarkIdentifier = $bookmark->getIdentifier();
            $bookmarkData = $bookmark->getConfig();
        } catch (NoSuchEntityException $e) {
            return [];
        }

        if (isset($bookmarkData['views'][$bookmarkIdentifier]['data']['paging'])) {
            $paging = $bookmarkData['views'][$bookmarkIdentifier]['data']['paging'];
            $gridState = $this->stateManager->get($namespace);

            if (isset($paging['pageSize'])) {
                $gridState->setLimit((int)$paging['pageSize']);
            }

            if (isset($paging['current'])) {
                $gridState->setPage((int)$paging['current']);
            }
        }

        if (false === isset($bookmarkData['views'][$bookmarkIdentifier]['data']['columns'])) {
            return [];
        }

        $positions = $bookmarkData['views'][$bookmarkIdentifier]['data']['positions'];

        $columns = [];
        foreach ($bookmarkData['views'][$bookmarkIdentifier]['data']['columns'] as $columnName => $columnData) {
            if ($columnName === 'actions') {
                continue;
            }

            if ((int)$columnData['visible'] === 1) {
                $columns[] = $this->columnFactory->create([
                    'code' => $columnName,
                    'position' => (isset($positions[$columnName])) ? $positions[$columnName] : 99,
                ]);
            }
        }

        return $columns;
    }

    public function createColumn(string $columnName): Column
    {
        return $this->columnFactory->create([
            'code' => $columnName,
        ]);
    }

    /**
     * @param Column[] $columns
     *
     * @return Column[]
     */
    public function sortColumns(array $columns): array
    {
        usort($columns, function (Column $a, Column $b) {
            if ($a->getPosition() === $b->getPosition()) {
                return 0;
            }

            if ($a->getPosition() < $b->getPosition()) {
                return -1;
            }

            return 1;
        });

        return $columns;
    }
}
