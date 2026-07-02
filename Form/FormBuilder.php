<?php declare(strict_types=1);

namespace Loki\AdminComponents\Form;

use Loki\AdminComponents\Form\Field\Field;
use Loki\AdminComponents\Form\Field\FieldFactory;
use Magento\Framework\View\Element\AbstractBlock;

class FormBuilder
{
    public function __construct(
        private FormFactory $formFactory,
        private FieldFactory $fieldFactory
    ) {
    }

    public function createForm(string $code): Form
    {
        return $this->formFactory->create($code);
    }

    public function createField(AbstractBlock $block, array $data = []): Field
    {
        return $this->fieldFactory->create($block, $data);
    }
}
