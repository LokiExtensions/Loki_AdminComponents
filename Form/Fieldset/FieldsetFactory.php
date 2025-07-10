<?php
declare(strict_types=1);

namespace Loki\AdminComponents\Form\Fieldset;

use Magento\Framework\ObjectManagerInterface;

class FieldsetFactory
{
    public function __construct(
        private ObjectManagerInterface $objectManager,
    ) {
    }

    public function create(string $label, array $fields = []): Fieldset
    {
        return $this->objectManager->create(Fieldset::class, [
            'label' => $label,
            'fields' => $fields,
        ]);
    }
}
