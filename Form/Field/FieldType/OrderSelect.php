<?php
declare(strict_types=1);

namespace Loki\AdminComponents\Form\Field\FieldType;

use Loki\AdminComponents\Form\Field\Field;
use Magento\Sales\Api\OrderRepositoryInterface;

class OrderSelect extends FieldTypeAbstract
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
    ) {
    }

    public function getInputType(): string
    {
        return 'text';
    }

    public function prepareField(Field $field): Field
    {
        $field->setProvider($this->orderRepository);
        $field->setButtonLabel('Select order');
        $field->setNamespace('sales_order_grid');
        return $field;
    }

    public function getTemplate(): string
    {
        return 'Loki_AdminComponents::form/field_type/entity_select.phtml';
    }
}
