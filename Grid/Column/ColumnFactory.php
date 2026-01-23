<?php declare(strict_types=1);

namespace Loki\AdminComponents\Grid\Column;

use Magento\Framework\ObjectManagerInterface;

class ColumnFactory
{
    public function __construct(
        private ObjectManagerInterface $objectManager
    ) {
    }

    public function create(array $data): Column
    {
        if (!isset($data['visible'])) {
            $data['visible'] = true;
        }

        return $this->objectManager->create(Column::class, ['data' => $data]);
    }
}
