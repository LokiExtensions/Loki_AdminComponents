<?php
declare(strict_types=1);

namespace Loki\AdminComponents\Form\Item;

use Magento\Framework\DataObject;
use Magento\Framework\View\Element\Block\ArgumentInterface;

interface ItemValidatorInterface extends ArgumentInterface
{
    public function validate(DataObject $item): true|array;
}
