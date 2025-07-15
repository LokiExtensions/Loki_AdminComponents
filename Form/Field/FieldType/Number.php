<?php declare(strict_types=1);

namespace Loki\AdminComponents\Form\Field\FieldType;

use Loki\AdminComponents\Form\Field\FieldTypeInterface;

class Number implements FieldTypeInterface
{
    public function __construct(
        private int $maximumLength = 255,
    ) {
    }

    public function getTemplate(): string
    {
        return 'Loki_AdminComponents::form/field_type/text.phtml';
    }

    public function getInputType(): string
    {
        return 'number';
    }

    public function getMaximumLength(): int
    {
        return $this->maximumLength;
    }
}
