<?php
declare(strict_types=1);

namespace Loki\AdminComponents\Form\Field\FieldType;

use Loki\AdminComponents\Form\Field\Field;
use Magento\Catalog\Model\ResourceModel\Product\Collection;

class ProductSelect extends FieldTypeAbstract
{
    public function getInputType(): string
    {
        return 'text';
    }

    public function prepareField(Field $field): Field
    {
        $field->setProvider(Collection::class);
        $field->setButtonLabel('Select product');
        $field->setNamespace('product_listing');
        return $field;
    }

    public function getTemplate(): string
    {
        return 'Loki_AdminComponents::form/field_type/entity_select.phtml';
    }
}
