<?php declare(strict_types=1);

namespace Loki\AdminComponents\Grid;

use Magento\Framework\Exception\NoSuchEntityException;

class ColumnLoader
{
    public function __construct(
        private BookmarkLoader $bookmarkLoader,
    ) {
    }

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

    public function getLabelByColumn(string $columnName): string
    {
        $label = (string)__($columnName);
        if ($label !== $columnName) {
            return $label;
        }

        return ucfirst(str_replace('_', ' ', $label));
    }
}
