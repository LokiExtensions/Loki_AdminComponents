<?php declare(strict_types=1);

namespace Loki\AdminComponents\Form\Field\FieldType;

use Loki\AdminComponents\Form\Field\FieldTypeInterface;

class Text implements FieldTypeInterface
{
    public function __construct(
        private string $inputType = 'text',
        private int $maximumLength = 255,
    ) {
    }

    public function getTemplate(): string
    {
        return 'Loki_AdminComponents::form/field_type/text.phtml';
    }

    public function getInputType(): string
    {
        return $this->inputType;
    }

    public function getMaximumLength(): int
    {
        return $this->maximumLength;
    }
}
