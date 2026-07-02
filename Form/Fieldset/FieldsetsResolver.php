<?php
declare(strict_types=1);

namespace Loki\AdminComponents\Form\Fieldset;

use Loki\AdminComponents\Component\Form\FormRepository;
use Loki\AdminComponents\Form\Field\FieldFactory;
use Loki\AdminComponents\Form\Field\FieldsResolver;
use Loki\AdminComponents\Provider\FormProviderInterface;
use Magento\Framework\View\Element\AbstractBlock;

class FieldsetsResolver
{
    public function __construct(
        private FieldsetFactory $fieldsetFactory,
        private FieldsResolver $fieldsResolver,
        private FieldFactory $fieldFactory
    ) {
    }

    /**
     * @return Fieldset[]
     */
    public function resolve(
        FormRepository $formRepository,
        AbstractBlock $block,
    ): array {
        $provider = $formRepository->getProvider();
        if ($provider instanceof FormProviderInterface) {
            return $provider->getForm()->getFieldsets();
        }

        $fieldsetDefinitions = (array)$block->getFieldsets();

        $fieldsetLabel = 'Base';
        if (isset($fieldsetDefinitions['base'])) {
            $fieldsetLabel = $fieldsetDefinitions['base']['label'] ?? $fieldsetDefinitions['base']['code'];
        }

        $fieldsets['base'] = $this->fieldsetFactory->create('base', $fieldsetLabel);

        foreach ($fieldsetDefinitions as $fieldsetCode => $fieldsetDefinition) {
            if (empty($fieldsetCode)) {
                $fieldsetCode = 'base';
            }

            if (array_key_exists($fieldsetCode, $fieldsets)) {
                continue;
            }

            $fieldsets[$fieldsetCode] = $this->fieldsetFactory->create($fieldsetCode, $fieldsetDefinition['label']);
        }

        $fieldDefinitions = (array)$block->getFields();

        $fields = $this->fieldsResolver->resolve($formRepository, $fieldDefinitions);

        foreach ($fields as $field) {
            $fieldsetCode = (string)$field->getFieldset();

            if (isset($fieldDefinitions[$field->getCode()])) {
                unset($fieldDefinitions[$field->getCode()]);
            }

            if (!empty($fieldsetCode) && array_key_exists($fieldsetCode, $fieldsets)) {
                $fieldsets[$fieldsetCode]->addField($field);
                continue;
            }

            $fieldsets['base']->addField($field);
        }

        foreach ($fieldDefinitions as $fieldDefinitionCode => $fieldDefinition) {
            if (!isset($fieldDefinition['code'])) {
                $fieldDefinition['code'] = $fieldDefinitionCode;
            }

            $field = $this->fieldFactory->create($this->getBlock(), $fieldDefinition);

            if (!empty($fieldsetCode) && array_key_exists($fieldsetCode, $fieldsets)) {
                $fieldsets[$fieldsetCode]->addField($field);
                continue;
            }

            $fieldsets['base']->addField($field);
        }

        return $fieldsets;
    }
}
