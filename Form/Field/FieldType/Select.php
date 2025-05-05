<?php declare(strict_types=1);

namespace Yireo\LokiAdminComponents\Form\Field\FieldType;

use Yireo\LokiAdminComponents\Form\Field\FieldTypeInterface;

class Select implements FieldTypeInterface
{
    public function getTemplate(): string
    {
        return 'Yireo_LokiAdminComponents::form/field_type/select.phtml';
    }
}
