<?php

declare(strict_types = 1);

namespace Loki\AdminComponents\Form\Field\FieldType;

use Loki\AdminComponents\Form\Field\Field;
use Loki\AdminComponents\Form\Field\FieldTypeInterface;

abstract class FieldTypeAbstract implements FieldTypeInterface
{
    protected ?Field $field = null;

    public function setField(Field $field): self
    {
        $this->field = $field;
        return $this;
    }

    public function getField(): ?Field
    {
        return $this->field;
    }
}
