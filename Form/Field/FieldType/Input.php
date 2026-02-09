<?php declare(strict_types=1);

namespace Loki\AdminComponents\Form\Field\FieldType;

class Input extends FieldTypeAbstract
{
    public function getTemplate(): string
    {
        return 'Loki_AdminComponents::form/field_type/input.phtml';
    }

    public function getInputType(): string
    {
        return 'text';
    }
}
