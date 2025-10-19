<?php
declare(strict_types=1);

namespace Loki\AdminComponents\Form\Field;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\LayoutInterface;

class FieldFactory
{
    public function __construct(
        private ObjectManagerInterface $objectManager,
        private FieldTypeProvider $fieldTypeProvider,
        private LayoutInterface $layout
    ) {
    }

    public function createWithBlock(
        array $data = []
    ) {
        $block = $this->layout->createBlock(Template::class);
        $block->setTemplate('Loki_AdminComponents::form/field.phtml');
        return $this->create($block, $data);
    }

    public function create(
        AbstractBlock $block,
        array $data = [],
    ): Field {
        // @todo: Move this to form field data sanitizer
        if (!isset($data['field_type']) || empty($data['field_type'])) {
            $data['field_type'] = 'input';
        }

        if (false === $data['field_type'] instanceof FieldTypeInterface) {
            $data['field_type'] = $this->fieldTypeProvider->getFieldTypeByCode($data['field_type']);
        }

        if (false === $data['field_type'] instanceof FieldTypeInterface) {
            throw new \RuntimeException((string)__('Field type "%1" could not be resolved', $data['field_type']));
        }

        if (!isset($data['scope'])) {
            $data['scope'] = 'item';
        }

        if (!isset($data['visible'])) {
            $data['visible'] = true;
        }

        return $this->objectManager->create(Field::class, [
            'block' => $block,
            'data' => $data,
        ]);
    }
}
