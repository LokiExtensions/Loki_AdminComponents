<?php declare(strict_types=1);

namespace Loki\AdminComponents\Form;

use Loki\AdminComponents\Form\Field\Field;
use Loki\AdminComponents\Form\Field\FieldFactory;
use Loki\AdminComponents\Form\Fieldset\Fieldset;
use Loki\AdminComponents\Form\Fieldset\FieldsetFactory;
use Loki\AdminComponents\Ui\Button\Button;
use Loki\AdminComponents\Ui\Button\ButtonFactory;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\LayoutInterface;

class FormBuilder
{
    public function __construct(
        private FormFactory $formFactory,
        private FieldFactory $fieldFactory,
        private FieldsetFactory $fieldsetFactory,
        private ButtonFactory $buttonFactory,
        private LayoutInterface $layout
    ) {
    }

    public function createForm(string $code): Form
    {
        return $this->formFactory->create($code);
    }

    public function createField(array $data = []): Field
    {
        $block = $this->layout->createBlock(Template::class);

        return $this->fieldFactory->create($block, $data);
    }

    public function createFieldset(string $code, string $label = '', array $fields = []): Fieldset
    {
        return $this->fieldsetFactory->create($code, $label, $fields);
    }

    public function createButton(
        string $method,
        string $label,
        string $cssClass = '',
        string $url = '',
        array $subButtons = [],
        bool $primary = false,
    ): Button {
        return $this->buttonFactory->create(
            $method,
            $label,
            $cssClass,
            $url,
            $subButtons,
            $primary
        );
    }
}
