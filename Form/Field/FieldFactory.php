<?php
declare(strict_types=1);

namespace Yireo\LokiAdminComponents\Form\Field;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\LayoutInterface;
use Yireo\LokiAdminComponents\Component\Form\FormViewModel;

class FieldFactory
{
    public function __construct(
        private ObjectManagerInterface $objectManager,
        private FieldTypeProvider $fieldTypeProvider,
        private LayoutInterface $layout
    ) {
    }

    public function createWithBlock(
        string $fieldTypeCode,
        string $label,
        string $code,
        bool $required = false,
    ) {
        $block = $this->layout->createBlock(Template::class);
        $block->setTemplate('Yireo_LokiAdminComponents::form/field.phtml');
        return $this->create($block, $fieldTypeCode, $label, $code, $required);
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
