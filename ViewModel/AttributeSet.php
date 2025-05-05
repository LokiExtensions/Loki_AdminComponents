<?php declare(strict_types=1);

namespace Yireo\LokiAdminComponents\ViewModel;

use Magento\Eav\Model\AttributeSetRepository;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class AttributeSet implements ArgumentInterface
{
    public function __construct(
        private AttributeSetRepository $attributeSetRepository
    ) {
    }

    public function getLabel(int $attributeSetId): string
    {
        return (string)$this->attributeSetRepository->get($attributeSetId)->getAttributeSetName();
    }
}
