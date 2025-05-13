<?php
declare(strict_types=1);

namespace Yireo\LokiAdminComponents\Form\Field;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Element\AbstractBlock;
use Yireo\LokiAdminComponents\Component\Form\FormViewModel;

class FieldFactory
{
    public function __construct(
        private ObjectManagerInterface $objectManager,
        private FieldTypeProvider $fieldTypeProvider,
    ) {
    }

    public function create(
        AbstractBlock $block,
        string $fieldTypeCode,
        string $label,
        string $code,
        bool $required = false,
    ): Field {
        $fieldType = $this->fieldTypeProvider->getFieldTypeByCode($fieldTypeCode);

        return $this->objectManager->create(Field::class, [
            'block' => $block,
            'fieldType' => $fieldType,
            'label' => $label,
            'code' => $code,
            'required' => $required,
        ]);
    }
}
