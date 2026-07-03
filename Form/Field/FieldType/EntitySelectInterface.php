<?php

namespace Loki\AdminComponents\Form\Field\FieldType;

use Magento\Customer\Model\Data\Customer;

interface EntitySelectInterface
{
    public function getIdField(): string;
    public function getPreviewValue($item): string;
    public function getPreviewTemplate(): string;
}
