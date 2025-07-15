<?php declare(strict_types=1);

namespace Loki\AdminComponents\ViewModel;

use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Loki\AdminComponents\Form\Field\Field;
use Loki\AdminComponents\Form\Field\FieldFactory;

class FieldViewModel implements ArgumentInterface
{
    public function __construct(
        private FieldFactory $fieldFactory,
    ) {
    }

    public function getField(AbstractBlock $block): Field
    {
        $blockName = $block->getNameInLayout();
        $blockParts = explode('.', $blockName);
        $blockAlias = array_pop($blockParts);

        $fieldType = !empty($block->getFieldType()) ? (string)$block->getFieldType() : 'text';
        $label = !empty($block->getLabel()) ? (string)$block->getLabel() : $blockAlias;
        $name = !empty($block->getName()) ? (string)$block->getName() : $blockAlias;
        $required = !empty($block->getRequired()) ? (bool)$block->getRequired() : false;

        return $this->fieldFactory->create($block, $fieldType, $label, $name, $required);
    }
}
