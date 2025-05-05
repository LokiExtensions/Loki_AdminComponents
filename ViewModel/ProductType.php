<?php declare(strict_types=1);

namespace Yireo\LokiAdminComponents\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;

class ProductType implements ArgumentInterface
{
    public function __construct(
        private \Magento\Catalog\Model\Product\Type $productType
    ) {
    }

    public function getLabel($productType): string
    {
        $productTypeOptions = $this->productType->getOptionArray();

        if (array_key_exists($productType, $productTypeOptions)) {
            return (string)$productTypeOptions[$productType];
        }

        return (string)$productType;
    }
}
