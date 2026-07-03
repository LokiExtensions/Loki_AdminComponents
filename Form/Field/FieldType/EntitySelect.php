<?php
declare(strict_types=1);

namespace Loki\AdminComponents\Form\Field\FieldType;

use Magento\Framework\DataObject;

class EntitySelect extends FieldTypeAbstract implements EntitySelectInterface
{
    public function getInputType(): string
    {
        return 'text';
    }

    public function getTemplate(): string
    {
        return 'Loki_AdminComponents::form/field_type/entity_select.phtml';
    }

    public function getIdField(): string
    {
        return 'id';
    }

    public function getPreviewValue($item): string
    {
        /** @var DataObject $item */
        return (string)$item->getName();
    }

    public function getPreviewTemplate(): string
    {
        return '%name%';
    }
}
