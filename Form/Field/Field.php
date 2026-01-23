<?php
declare(strict_types=1);

namespace Loki\AdminComponents\Form\Field;

use Loki\AdminComponents\Form\Field\FieldType\Editor;
use Magento\Framework\DataObject;
use Magento\Framework\View\Element\AbstractBlock;

class Field extends DataObject
{
    public function __construct(
        private AbstractBlock $block,
        array $data = [],
    ) {
        parent::__construct($data);
    }

    public function getLabel(): string
    {
        return (string)$this->getData('label');
    }

    public function getCode(): string
    {
        return (string)$this->getData('code');
    }

    public function isRequired(): bool
    {
        return (bool)$this->getData('required');
    }

    public function allowHtml(): bool
    {
        if ($this->getFieldType() instanceof Editor) {
            return true;
        }

        return (bool)$this->getData('allow_html');
    }

    public function getScope(): string
    {
        return (string)$this->getData('scope');
    }

    public function getAlpineSetter(): string
    {
        $alpineSetter = (string)$this->getData('alpine_setter');
        if ($alpineSetter) {
            return $alpineSetter;
        }

        return 'setValue';
    }

    public function getFieldType(): FieldTypeInterface
    {
        return $this->getData('field_type')->setField($this);
    }

    public function getBlock(): AbstractBlock
    {
        return $this->block;
    }

    public function getFieldAttributes(): array
    {
        return (array)$this->getData('field_attributes');
    }

    public function getLabelAttributes(): array
    {
        return (array)$this->getData('label_attributes');
    }


    public function getSortOrder(): int
    {
        return (int)$this->getData('sort_order');
    }

    public function isVisible(): bool
    {
        $visible = $this->getData('visible');
        if (is_bool($visible)) {
            return $visible;
        }

        return true;
    }
}
