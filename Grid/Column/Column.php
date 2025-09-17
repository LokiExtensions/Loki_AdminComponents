<?php declare(strict_types=1);

namespace Loki\AdminComponents\Grid\Column;

use Magento\Framework\DataObject;

class Column extends DataObject
{
    public function getLabel(): string
    {
        return (string)$this->getData('label');
    }

    public function getCode(): string
    {
        return (string)$this->getData('code');
    }

    public function getCellTemplate(): string
    {
        return (string)$this->getData('cell_template');
    }

    public function __toString(): string
    {
        return $this->getCode();
    }
}
