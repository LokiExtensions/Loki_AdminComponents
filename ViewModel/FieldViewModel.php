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

        $data = [];
        $data['field_type'] = !empty($block->getFieldType()) ? (string)$block->getFieldType() : 'input';
        $data['label'] = !empty($block->getLabel()) ? (string)$block->getLabel() : $blockAlias;
        $data['name'] = !empty($block->getName()) ? (string)$block->getName() : $blockAlias;
        $data['required'] = !empty($block->getRequired()) ? (bool)$block->getRequired() : false;

        return $this->fieldFactory->create($block, $data);
    }
}
