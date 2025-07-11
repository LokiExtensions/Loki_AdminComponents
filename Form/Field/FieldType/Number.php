<?php declare(strict_types=1);

namespace Loki\AdminComponents\Form\Field\FieldType;

class Number extends Text
{
    public function getInputType(): string
    {
        return 'number';
    }
}
