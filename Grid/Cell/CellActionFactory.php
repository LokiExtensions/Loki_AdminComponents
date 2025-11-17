<?php declare(strict_types=1);

namespace Loki\AdminComponents\Grid\Cell;

use Magento\Framework\ObjectManagerInterface;

class CellActionFactory
{
    public function __construct(
        private ObjectManagerInterface $objectManager,
    ) {
    }

    public function create(string $label, ?string $url = null, ?string $jsMethod = null): CellAction
    {
        return $this->objectManager->create(CellAction::class, [
            'label' => $label,
            'url' => $url,
            'jsMethod' => $jsMethod,
        ]);
    }
}
