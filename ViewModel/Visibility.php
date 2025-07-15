<?php declare(strict_types=1);

namespace Loki\AdminComponents\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;

class Visibility implements ArgumentInterface
{
    public function getLabel($visibility): string
    {
        $visibilityOptions = \Magento\Catalog\Model\Product\Visibility::getOptionArray();

        if (array_key_exists($visibility, $visibilityOptions)) {
            return (string)$visibilityOptions[$visibility];
        }

        return (string)$visibility;
    }
}
