<?php
declare(strict_types=1);

namespace Loki\AdminComponents\Form\Field\FieldType;

use Loki\AdminComponents\Form\Field\Field;
use Loki\AdminComponents\Form\Field\FieldTypeInterface;
use Magento\Customer\Model\ResourceModel\Customer\Collection;

class CustomerSelect implements FieldTypeInterface
{
    public function getInputType(): string
    {
        return 'text';
    }

    public function prepareField(Field $field): Field
    {
        $field->setProvider(Collection::class);
        $field->setButtonLabel('Select customer');
        $field->setNamespace('customer_listing');
        return $field;
    }

    public function getTemplate(): string
    {
        return 'Loki_AdminComponents::form/field_type/entity_select.phtml';
    }
}
