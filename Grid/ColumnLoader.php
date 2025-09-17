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
        static $columns = false;
        if (is_array($columns)) {
            return $columns;
        }

        try {
            $bookmark = $this->bookmarkLoader->getBookmark($namespace);
            $bookmarkData = $bookmark->getConfig();
        } catch (NoSuchEntityException $e) {
            $bookmarkData = [];
        }

        // @todo $bookmarkData['views']['default']['data']['paging']['options']

        if (false === isset($bookmarkData['views']['default']['data']['columns'])) {
            return [];
        }

        $positions = $bookmarkData['views']['default']['data']['positions'];

        $columns = [];
        foreach ($bookmarkData['views']['default']['data']['columns'] as $columnName => $columnData) {
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
}
