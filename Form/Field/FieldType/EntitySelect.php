<?php
declare(strict_types=1);

namespace Loki\AdminComponents\Form\Field\FieldType;

use Loki\AdminComponents\Form\Field\FieldTypeInterface;

class EntitySelect extends FieldTypeAbstract implements FieldTypeInterface
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
