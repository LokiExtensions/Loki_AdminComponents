<?php
declare(strict_types=1);

namespace Loki\AdminComponents\Provider;

use Loki\AdminComponents\Form\Form;
use Magento\Framework\DataObject;

interface ItemProviderInterface
{
    public function getItem(int|string $identifier): DataObject;

    public function saveItem(DataObject $item): void;

    public function deleteItem(DataObject $item): void;

    public function duplicateItem(DataObject $item): void;
}
