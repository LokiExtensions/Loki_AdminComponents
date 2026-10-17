<?php
declare(strict_types=1);

namespace Loki\AdminComponents\Form\Fieldset;

use Loki\AdminComponents\Component\Form\FormRepository;
use Loki\AdminComponents\Form\Field\Field;
use Loki\AdminComponents\Form\Field\FieldFactory;
use Loki\AdminComponents\Form\Field\FieldsResolver;
use Loki\AdminComponents\Form\Fieldset\FieldsetsResolver\FieldsetsResolverInterface;
use Magento\Framework\View\Element\AbstractBlock;

class FieldsetsResolver
{
    public function __construct(
        private FieldsetFactory $fieldsetFactory,
        private FieldsResolver $fieldsResolver,
        private FieldFactory $fieldFactory,
        /** @var FieldsetsResolverInterface[] */ private array $fieldsetsResolvers = []
    ) {
    }

    /**
     * @return Fieldset[]
     */
    public function resolve(
        FormRepository $formRepository,
        AbstractBlock $block,
    ): array {
        $fieldsets = [];
        foreach ($this->fieldsetsResolvers as $fieldsetsResolver) {
            $fieldsets = $fieldsetsResolver->resolve($formRepository, $fieldsets);
        }

        $fieldsetDefinitions = (array)$block->getFieldsets();

        if (!array_key_exists('base', $fieldsets)) {
            $fieldsets['base'] = $this->fieldsetFactory->create(
                'base',
                $this->getFieldsetLabel('base', $fieldsetDefinitions['base'] ?? [])
            );
        }

        foreach ($fieldsetDefinitions as $fieldsetCode => $fieldsetDefinition) {
            if (empty($fieldsetCode)) {
                $fieldsetCode = 'base';
            }

            if (array_key_exists($fieldsetCode, $fieldsets)) {
                continue;
            }

            $fieldsets[$fieldsetCode] = $this->fieldsetFactory->create(
                (string)$fieldsetCode,
                $this->getFieldsetLabel((string)$fieldsetCode, (array)$fieldsetDefinition)
            );
        }

        $fieldDefinitions = (array)$block->getFields();

        $fields = $this->fieldsResolver->resolve($formRepository, $fieldDefinitions);

        foreach ($fields as $field) {
            if (isset($fieldDefinitions[$field->getCode()])) {
                unset($fieldDefinitions[$field->getCode()]);
            }

            $this->addFieldToFieldsets($fieldsets, $field, (string)$field->getFieldset());
        }

        foreach ($fieldDefinitions as $fieldDefinitionCode => $fieldDefinition) {
            if (!isset($fieldDefinition['code'])) {
                $fieldDefinition['code'] = $fieldDefinitionCode;
            }

            $field = $this->fieldFactory->create($block, $fieldDefinition);
            $this->addFieldToFieldsets($fieldsets, $field, (string)($fieldDefinition['fieldset'] ?? ''));
        }

        return array_filter($fieldsets, fn (Fieldset $fieldset) => $fieldset->hasFields());
    }

    /**
     * @param Fieldset[] $fieldsets
     */
    private function addFieldToFieldsets(array $fieldsets, Field $field, string $fieldsetCode): void
    {
        if (!empty($fieldsetCode) && array_key_exists($fieldsetCode, $fieldsets)) {
            $fieldsets[$fieldsetCode]->addField($field);
            return;
        }

        $fieldsets['base']->addField($field);
    }

    private function getFieldsetLabel(string $fieldsetCode, array $fieldsetDefinition): string
    {
        if (!empty($fieldsetDefinition['label'])) {
            return (string)$fieldsetDefinition['label'];
        }

        if ($fieldsetCode === 'base') {
            return 'Base';
        }

        return (string)($fieldsetDefinition['code'] ?? $fieldsetCode);
    }
}
