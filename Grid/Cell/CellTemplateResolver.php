<?php declare(strict_types=1);

namespace Loki\AdminComponents\Grid\Cell;

use Magento\Framework\DataObject;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\LayoutInterface;

class CellTemplateResolver
{
    public function __construct(
        private LayoutInterface $layout,
        private array $cellTemplates = []
    ) {
    }

    public function resolve(DataObject $item, string $cellName, string $namespace)
    {
        if (array_key_exists($cellName, $this->cellTemplates)) {
            return $this->cellTemplates[$cellName];
        }

        $blockName = $namespace.'.'.$cellName;
        $block = $this->layout->getBlock($blockName);
        if ($block instanceof Template) {
            return $block->getTemplate();
        }

        return 'Loki_AdminComponents::grid/cell/default.phtml';
    }
}
