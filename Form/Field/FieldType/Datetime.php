<?php declare(strict_types=1);

namespace Loki\AdminComponents\Form\Field\FieldType;

class Datetime extends Input
{
    public function getInputType(): string
    {
        return 'datetime-local';
    }
}
