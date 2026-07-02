<?php
declare(strict_types=1);

namespace Loki\AdminComponents\Form\Fieldset;

use Loki\AdminComponents\Form\Field\Field;

class Fieldset
{
    public function __construct(
        private string $code,
        private string $label = '',
        private array $fields = []
    ) {
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function hasLabel(): bool
    {
        return !empty($this->label);
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function hasFields(): bool
    {
        return !empty($this->fields);
    }

    public function addFields(array $fields): Fieldset
    {
        foreach ($fields as $field) {
            $this->addField($field);
        }

        return $this;
    }

    public function addField(Field $field): Fieldset
    {
        $this->fields[] = $field;
        return $this;
    }
}
