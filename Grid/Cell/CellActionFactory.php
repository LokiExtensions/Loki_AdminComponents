<?php declare(strict_types=1);

namespace Loki\AdminComponents\Grid\Cell;

use Magento\Framework\ObjectManagerInterface;

class CellActionFactory
{
    public function __construct(
        private ObjectManagerInterface $objectManager,
    ) {
    }

    public function create(string $url, string $label): CellAction
    {
        return $this->objectManager->create(CellAction::class, [
            'url' => $url,
            'label' => $label,
        ]);
    }
}
