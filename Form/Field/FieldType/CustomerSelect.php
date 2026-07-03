<?php
declare(strict_types=1);

namespace Loki\AdminComponents\Form\Field\FieldType;

use Loki\AdminComponents\Form\Field\Field;
use Magento\Customer\Model\Data\Customer;
use Magento\Customer\Model\ResourceModel\Customer\Collection;

class CustomerSelect extends FieldTypeAbstract implements EntitySelectInterface
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

    public function getIdField(): string
    {
        return 'entity_id';
    }

    public function getPreviewValue($item): string
    {
        /** @var Customer $item */
        return $item->getFirstname().' '.$item->getLastname().' ('.$item->getEmail().')';
    }

    public function getPreviewTemplate(): string
    {
        return '%name% (%email%)';
    }

    public function getTemplate(): string
    {
        return 'Loki_AdminComponents::form/field_type/entity_select.phtml';
    }
}
