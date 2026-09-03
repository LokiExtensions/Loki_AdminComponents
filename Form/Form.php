<?php
declare(strict_types=1);

namespace Loki\AdminComponents\Form;

use Loki\AdminComponents\Form\Field\Field;
use Loki\AdminComponents\Form\Field\FieldFactory;
use Loki\AdminComponents\Form\Fieldset\Fieldset;
use Loki\AdminComponents\Form\Fieldset\FieldsetFactory;
use Loki\AdminComponents\Ui\Button\Button;
use Loki\AdminComponents\Ui\Button\ButtonFactory;

class Form
{
    public function __construct(
        protected FieldFactory $fieldFactory,
        protected FieldsetFactory $fieldsetFactory,
        protected ButtonFactory $buttonFactory,
    ) {
    }

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
