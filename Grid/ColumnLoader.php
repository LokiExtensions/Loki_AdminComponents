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
        static $flatColumns = false;
        if (is_array($flatColumns)) {
            return $flatColumns;
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
                $columns[] = [
                    'name' => $columnName,
                    'position' => (isset($positions[$columnName])) ? $positions[$columnName] : 99,
                    'label' => $this->getLabelByColumn($columnName),
                ];
            }
        }

        usort($columns, function ($a, $b) {
            if ($a['position'] === $b['position']) {
                return 0;
            }

            if ($a['position'] < $b['position']) {
                return -1;
            }

            return 1;
        });


        $flatColumns = [];
        foreach ($columns as $column) {
            $columnName = $column['name'];
            $flatColumns[$columnName] = $column['label'];
        }

        return $flatColumns;
    }

    public function getLabelByColumn(string $columnName): Column
    {
        $label = (string)__($columnName);
        if ($label === $columnName) {
            $label = ucfirst(str_replace('_', ' ', $label));
        }

        return $this->columnFactory->create([
            'code' => $columnName,
            'label' => $label,
        ]);
    }
}
