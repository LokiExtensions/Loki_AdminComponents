<?php
declare(strict_types=1);

namespace Loki\AdminComponents\Form\Field\FieldType;

class EntitySelect extends FieldTypeAbstract
{
    public function getInputType(): string
    {
        return 'text';
    }

    public function getTemplate(): string
    {
        return 'Loki_AdminComponents::form/field_type/entity_select.phtml';
    }
}
