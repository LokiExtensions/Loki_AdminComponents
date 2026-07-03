<?php
declare(strict_types=1);

namespace Loki\AdminComponents\Form\Field\FieldType;

use Loki\AdminComponents\Form\Field\Field;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Product\Collection;

class ProductSelect extends FieldTypeAbstract implements EntitySelectInterface
{
    public function getInputType(): string
    {
        return 'text';
    }

    public function prepareField(Field $field): Field
    {
        $field->setProvider(ProductRepositoryInterface::class);
        $field->setButtonLabel('Select product');
        $field->setNamespace('product_listing');
        return $field;
    }

    public function getIdField(): string
    {
        return 'entity_id';
    }

    public function getPreviewValue($item): string
    {
        /** @var ProductInterface $item */
        return $item->getName().' ('.$item->getSku().')';
    }

    public function getPreviewTemplate(): string
    {
        return '%name% (%sku%)';
    }

    public function getTemplate(): string
    {
        return 'Loki_AdminComponents::form/field_type/entity_select.phtml';
    }
}
