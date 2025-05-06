<?php declare(strict_types=1);

namespace Yireo\LokiAdminComponents\Grid\Cell;

use Magento\Framework\DataObject;

class CellTemplateResolver
{
    public function __construct(
        private array $cellTemplates = []
    ) {
    }

    public function resolve(DataObject $item, string $cellName)
    {
        if (array_key_exists($cellName, $this->cellTemplates)) {
            return $this->cellTemplates[$cellName];
        }

        return 'Yireo_LokiAdminComponents::grid/cell/default.phtml';
    }
}
