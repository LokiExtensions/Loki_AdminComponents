<?php declare(strict_types=1);

namespace Loki\AdminComponents\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;

class Status implements ArgumentInterface
{
    public function getLabel($status): string
    {
        $statusOptions = \Magento\Catalog\Model\Product\Attribute\Source\Status::getOptionArray();

        if (array_key_exists($status, $statusOptions)) {
            return (string)$statusOptions[$status];
        }

        return (string)$status;
    }
}
