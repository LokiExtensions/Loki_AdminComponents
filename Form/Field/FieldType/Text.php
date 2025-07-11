<?php declare(strict_types=1);

namespace Loki\AdminComponents\Form\Field\FieldType;

use Loki\AdminComponents\Form\Field\FieldTypeInterface;

class Text implements FieldTypeInterface
{
    public function getTemplate(): string
    {
        return 'Loki_AdminComponents::form/field_type/text.phtml';
    }

    public function getInputType(): string
    {
        return 'text';
    }
}
