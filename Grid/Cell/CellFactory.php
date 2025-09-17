<?php declare(strict_types=1);

namespace Loki\AdminComponents\Grid\Cell;

use Magento\Framework\ObjectManagerInterface;

class CellFactory
{
    public function __construct(
        private ObjectManagerInterface $objectManager
    ){
    }

    public function create(string $code, string $label): Cell
    {
        return $this->objectManager->create(Cell::class, [
        ]);
    }
}
