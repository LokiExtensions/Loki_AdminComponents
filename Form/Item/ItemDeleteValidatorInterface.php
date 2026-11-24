<?php
declare(strict_types=1);

namespace Loki\AdminComponents\Form\Item;

use Magento\Framework\DataObject;
use Magento\Framework\View\Element\Block\ArgumentInterface;

interface ItemDeleteValidatorInterface extends ArgumentInterface
{
    public function validateDelete(DataObject $item): true|array;
}
