<?php declare(strict_types=1);

namespace Yireo\LokiAdminComponents\Form\Fieldset\FieldType;

use Yireo\LokiAdminComponents\Form\Fieldset\FieldTypeInterface;

class Select implements FieldTypeInterface
{
    public function getTemplate(): string
    {
        return 'Yireo_LokiAdminComponents::form/field_type/select.phtml';
    }
}
