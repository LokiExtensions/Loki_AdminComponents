<?php declare(strict_types=1);

namespace Loki\AdminComponents\Form\Field\FieldType;

use Loki\AdminComponents\Form\Field\FieldTypeInterface;

class Textarea extends FieldTypeAbstract implements FieldTypeInterface
{
    public function getTemplate(): string
    {
        return 'Loki_AdminComponents::form/field_type/textarea.phtml';
    }
}
