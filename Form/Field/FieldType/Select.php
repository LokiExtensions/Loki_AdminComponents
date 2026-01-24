<?php declare(strict_types=1);

namespace Loki\AdminComponents\Form\Field\FieldType;

use Loki\AdminComponents\Form\Field\Field;
use Loki\AdminComponents\Form\Field\FieldTypeInterface;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\ObjectManagerInterface;

class Select extends FieldTypeAbstract implements FieldTypeInterface
{
    public function __construct(
        private readonly ObjectManagerInterface $objectManager,
    ) {
    }

    public function getTemplate(): string
    {
        return 'Loki_AdminComponents::form/field_type/select.phtml';
    }

    public function getOptions(): array
    {
        if (!$this->field) {
            return [];
        }

        $options = $this->field->getOptions();

        if (!$options) {
            return [];
        }

        $options  = $this->objectManager->get($options);

        if (!$options instanceof OptionSourceInterface) {
            return [];
        }

        return $options->toOptionArray();
    }
}
