<?php
declare(strict_types=1);

namespace Loki\AdminComponents\Form\Fieldset;

class Fieldset
{
    public function __construct(
        private string $label,
    ) {
    }

    public function getLabel(): string
    {
        return $this->label;
    }
}
