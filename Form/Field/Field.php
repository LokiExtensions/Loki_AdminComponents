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
        private bool $required = false,
        private string $scope = 'item'
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

    public function getScope(): string
    {
        return $this->scope;
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
