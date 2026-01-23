<?php
declare(strict_types=1);

namespace Loki\AdminComponents\Form\Field\FieldType;

use Loki\AdminComponents\Form\Field\FieldTypeInterface;
use Loki\AdminComponents\ViewModel\Options\ProductOptions;

class ProductId extends FieldTypeAbstract implements FieldTypeInterface
{
    public function __construct(
        private ProductOptions $productOptions
    ) {
    }

    public function getTemplate(): string
    {
        return 'Loki_AdminComponents::form/field_type/product_id.phtml';
    }

    public function getOptions(): ProductOptions
    {
        return $this->productOptions;
    }
}
