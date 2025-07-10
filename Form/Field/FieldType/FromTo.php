<?php
declare(strict_types=1);

namespace Loki\AdminComponents\Form\Field\FieldType;

use Loki\AdminComponents\Form\Field\FieldTypeInterface;

class FromTo implements FieldTypeInterface
{
    public function __construct(
        private int $maximumLength = 255,
    ) {
    }

    public function getTemplate(): string
    {
        return 'Loki_AdminComponents::form/field_type/from_to.phtml';
    }

    public function getMaximumLength(): int
    {
        return $this->maximumLength;
    }
}
