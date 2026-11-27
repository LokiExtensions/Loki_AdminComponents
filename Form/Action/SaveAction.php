<?php declare(strict_types=1);

namespace Loki\AdminComponents\Form\Action;

use Loki\AdminComponents\Component\Form\FormViewModel;
use Loki\AdminComponents\Exception\FormValidationException;
use Loki\AdminComponents\Form\Field\Field;
use Loki\AdminComponents\Form\Field\FieldType\View;
use Loki\AdminComponents\Form\Item\ItemConvertorInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Message\Manager;
use Loki\AdminComponents\Component\Form\FormRepository;
use Loki\Components\Util\CamelCaseConvertor;

class SaveAction implements ActionInterface
{
    public function __construct(
        private Manager $messageManager,
        private CamelCaseConvertor $camelCaseConvertor,
    ) {
    }

    public function execute(FormRepository $formRepository, array $value): void
    {
        $primaryKey = $formRepository->getPrimaryKey();
        $provider = $formRepository->getProvider();
        $providerHandler = $formRepository->getProviderHandler();

        $item = $formRepository->getItemFromData($value);
        $editableProperties = $this->getEditableProperties($formRepository);
        foreach ((array)($value['item'] ?? []) as $propertyKey => $propertyValue) {
            $propertyKey = (string)$propertyKey;
            if ($propertyKey === $primaryKey || !in_array($propertyKey, $editableProperties, true)) {
                continue;
            }

            $this->setItemPropertyValue($item, $propertyKey, $propertyValue);
        }

        $itemConvertor = $formRepository->getComponent()->getViewModel()->getBlock()->getItemConvertor();
        if ($itemConvertor instanceof ItemConvertorInterface) {
            $item = $itemConvertor->beforeSave($item);
        }

        $errorMessages = [];
        foreach ($formRepository->getItemValidators() as $itemValidator) {
            $validationResult = $itemValidator->validate($item);
            if ($validationResult === true) {
                continue;
            }

            $errorMessages = array_merge($errorMessages, $validationResult);
        }

        if (!empty($errorMessages)) {
            foreach ($errorMessages as $errorMessage) {
                $this->messageManager->addErrorMessage($errorMessage);
            }

            throw new FormValidationException();
        }

        $providerHandler->saveItem($provider, $item);
        $this->messageManager->addSuccessMessage('Item saved');
    }

    /**
     * @return string[]
     */
    private function getEditableProperties(FormRepository $formRepository): array
    {
        $viewModel = $formRepository->getComponent()->getViewModel();
        if (false === $viewModel instanceof FormViewModel) {
            return [];
        }

        $editableProperties = [];
        foreach ($viewModel->getFieldsets() as $fieldset) {
            foreach ($fieldset->getFields() as $field) {
                if (false === $this->isEditable($field)) {
                    continue;
                }

                $editableProperties[] = $field->getCode();
            }
        }

        return $editableProperties;
    }

    private function isEditable(Field $field): bool
    {
        if ($field->getData('field_type') instanceof View) {
            return false;
        }

        $editable = $field->getData('editable');
        if (is_bool($editable)) {
            return $editable;
        }

        return $field->isVisible() && false === $field->isHidden();
    }

    private function setItemPropertyValue(DataObject $item, $propertyName, $propertyValue): void
    {
        $propertyMethod = 'set'.ucfirst($this->camelCaseConvertor->toCamelCase($propertyName));

        call_user_func([$item, $propertyMethod], $propertyValue);
    }
}
