<?php
declare(strict_types=1);

namespace Loki\AdminComponents\Form;

use Loki\AdminComponents\Form\Field\Field;
use Loki\AdminComponents\Form\Fieldset\Fieldset;
use Loki\AdminComponents\Ui\Button;

class Form
{
    /**
     * @var Field[]
     */
    private array $fields = [];

    /**
     * @var Fieldset[]
     */
    private array $fieldsets = [];

    /**
     * @var Button[]
     */
    private array $buttons = [];

    public function addField(Field $field): Form
    {
        $this->fields[] = $field;
        return $this;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

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
