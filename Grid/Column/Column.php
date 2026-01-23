<?php declare(strict_types=1);

namespace Loki\AdminComponents\Grid\Column;

use JsonSerializable;
use Magento\Framework\DataObject;

class Column extends DataObject implements JsonSerializable
{
    public function getLabel(): string
    {
        $label = (string)$this->getData('label');
        if (!empty($label)) {
            return $label;
        }

        $label = (string)__($this->getCode());
        if ($label !== $this->getCode()) {
            return $label;
        }

        return ucfirst(str_replace('_', ' ', $label));
    }

    public function getCode(): string
    {
        $code = (string)$this->getData('code');
        if (!empty($code)) {
            return $code;
        }

        return 'unknown';
    }

    public function getCellTemplate(): string
    {
        return (string)$this->getData('cell_template');
    }

    public function getPosition(): int
    {
        return (int)$this->getData('position');
    }

    public function isVisible(): bool
    {
        return (bool)$this->getData('visible');
    }

    public function __toString(): string
    {
        return $this->getCode();
    }

    public function jsonSerialize(): mixed
    {
        return [
            'code' => $this->getCode(),
            'label' => $this->getLabel(),
            'position' => $this->getPosition(),
        ];
    }
}
