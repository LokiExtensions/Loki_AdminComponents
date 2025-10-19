<?php
declare(strict_types=1);

namespace Loki\AdminComponents\Grid\State;

class FilterState implements \JsonSerializable
{
    public function __construct(
        private string $field,
        private string $value,
        private string $conditionType,
    ) {
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getConditionType(): string
    {
        return $this->conditionType;
    }

    public function __toString()
    {
        return $this->value;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'field' => $this->field,
            'value' => $this->value,
            'condition_type' => $this->conditionType,
        ];
    }
}
