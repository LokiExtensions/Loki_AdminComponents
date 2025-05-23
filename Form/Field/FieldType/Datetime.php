<?php declare(strict_types=1);

namespace Yireo\LokiAdminComponents\Form\Field\FieldType;

use Yireo\LokiAdminComponents\Form\Field\FieldTypeInterface;

class Datetime implements FieldTypeInterface
{
    public function getTemplate(): string
    {
        return 'Yireo_LokiAdminComponents::form/field_type/text.phtml';
    }

    public function getInputType(): string
    {
        return 'datetime-local';
    }

    public function getMaximumLength(): int
    {
        return 100;
    }
}
