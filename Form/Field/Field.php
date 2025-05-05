<?php
declare(strict_types=1);

namespace Yireo\LokiAdminComponents\Form\Field;

use Magento\Framework\View\Element\AbstractBlock;

class Field
{
    public function __construct(
        private AbstractBlock $block,
        private FieldTypeInterface $fieldType,
        private string $label,
        private string $code,
        private bool $required,
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

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function getFieldType(): FieldTypeInterface
    {
        return $this->fieldType;
    }

    public function getBlock(): AbstractBlock
    {
        return $this->block;
    }
}
