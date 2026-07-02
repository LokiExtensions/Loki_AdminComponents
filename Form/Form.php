<?php
declare(strict_types=1);

namespace Loki\AdminComponents\Form;

use Loki\AdminComponents\Form\Field\Field;
use Loki\AdminComponents\Form\Fieldset\Fieldset;
use Loki\AdminComponents\Ui\Button\Button;

class Form
{
    /**
     * @var Fieldset[]
     */
    private array $fieldsets = [];

    /**
     * @var Button[]
     */
    private array $buttons = [];

    public function addFieldset(Fieldset $fieldset): Form
    {
        $this->fieldsets[] = $fieldset;
        return $this;
    }

    public function getFieldsets(): array
    {
        return $this->fieldsets;
    }

    public function addButton(Button $button): Form
    {
        $this->buttons[] = $button;
        return $this;
    }

    public function getButtons(): array
    {
        return $this->buttons;
    }
}
