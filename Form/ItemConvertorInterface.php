<?php
declare(strict_types=1);

namespace Loki\AdminComponents\Form;

use Magento\Framework\DataObject;
use Magento\Framework\View\Element\Block\ArgumentInterface;

interface ItemConvertorInterface extends ArgumentInterface
{
    public function afterLoad(DataObject $item): DataObject;
    public function beforeSave(DataObject $item): DataObject;
}
