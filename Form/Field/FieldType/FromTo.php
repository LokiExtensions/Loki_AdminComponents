<?php
declare(strict_types=1);

namespace Yireo\LokiAdminComponents\Form\Field\FieldType;

use Yireo\LokiAdminComponents\Form\Field\FieldTypeInterface;

class FromTo implements FieldTypeInterface
{
    public function __construct(
        private int $maximumLength = 255,
    ) {
    }

    public function getTemplate(): string
    {
        return 'Yireo_LokiAdminComponents::form/field_type/from_to.phtml';
    }

    public function getMaximumLength(): int
    {
        return $this->maximumLength;
    }
}
