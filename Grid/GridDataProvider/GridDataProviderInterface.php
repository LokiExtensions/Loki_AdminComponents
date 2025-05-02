<?php
declare(strict_types=1);

namespace Yireo\LokiAdminComponents\Grid\GridDataProvider;

use Magento\Framework\DataObject;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Block\ArgumentInterface;

interface GridDataProviderInterface extends ArgumentInterface
{
    public function setBlock(AbstractBlock $block): GridDataProviderInterface;

    /**
     * @return DataObject[]
     */
    public function getItems(): array;

}
