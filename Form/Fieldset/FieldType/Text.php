<?php declare(strict_types=1);

namespace Yireo\LokiAdminComponents\Form\Fieldset\FieldType;

use Yireo\LokiAdminComponents\Form\Fieldset\FieldTypeInterface;

class Text implements FieldTypeInterface
{
    public function __construct(
        private string $inputType = 'text',
        private int $maximumLength = 11,
    ) {
    }

    public function getTemplate(): string
    {
        return 'Yireo_LokiAdminComponents::form/field_type/text.phtml';
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
