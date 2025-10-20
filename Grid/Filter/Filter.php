<?php declare(strict_types=1);

namespace Loki\AdminComponents\Grid\Filter;

use Loki\AdminComponents\Form\Field\Field;
use Loki\AdminComponents\Form\Field\FieldFactory;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\LayoutInterface;

class Filter implements FilterInterface
{
    public function __construct(
        private LayoutInterface $layout,
        private FieldFactory $fieldFactory,
        private string $label,
        private string $code,
        private string $conditionType,
        private array $data = []
    ) {
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getConditionType(): string
    {
        return $this->conditionType;
    }

    public function render(): string
    {
        $block = $this->layout->createBlock(Template::class);
        $block->setTemplate('Loki_AdminComponents::form/field.phtml');
        $block->setField($this->getFormField($block));

        return $block->toHtml();
    }

    private function getFormField(Template $block): Field
    {
        $fieldType = $this->getFieldType();
        $field = $this->fieldFactory->create($block, [
            'scope' => 'gridFilters',
            'alpine_setter' => 'setGridFilter',
            'field_type' => $fieldType,
        ]);

        $data = $this->data;

        if (isset($data['field_type'])) {
            unset($data['field_type']);
        }

        $field->addData($data);

        return $field;
    }

    private function getFieldType(): string
    {
        if (isset($this->data['field_type']) && is_string($this->data['field_type'])) {
            return $this->data['field_type'];
        }

        if (isset($this->data['options'])) {
            return 'select';
        }

        return 'input';
    }

    public function getDefaultValue(): mixed
    {
        if ($this->getConditionType() === 'from_to') {
            return [
                'from' => null,
                'to' => null,
            ];
        }

        return null;
    }

    public function isEmpty(mixed $value): bool
    {
        if ($this->getConditionType() === 'from_to'
            && is_array($value)
            && empty($value['from'])
            && empty($value['to'])) {
            return true;
        }

        return $value === null;
    }
}
