<?php
declare(strict_types=1);

namespace Loki\AdminComponents\Form\Item;

use Magento\Framework\DataObject;
use Magento\Framework\ObjectManagerInterface;

class ItemFactory
{
    public function __construct(
        private ObjectManagerInterface $objectManager,
    ) {
    }

    public function create(array $data): DataObject
    {
        return $this->objectManager->create(DataObject::class, ['data' => $data]);
    }
}
