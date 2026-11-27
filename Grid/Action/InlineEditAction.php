<?php declare(strict_types=1);

namespace Loki\AdminComponents\Grid\Action;

use Loki\AdminComponents\Component\Grid\GridRepository;
use Loki\AdminComponents\Component\Grid\GridViewModel;
use Loki\AdminComponents\Form\Item\ItemValidatorInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;

class InlineEditAction implements ActionInterface
{
    public const INLINE_EDIT_TEMPLATE = 'Loki_AdminComponents::grid/cell/inline_edit.phtml';

    public function __construct(
        private readonly MessageManagerInterface $messageManager
    ) {
    }

    public function execute(GridRepository $gridRepository, array $value): void
    {
        if (!isset($value['inline_edit']) || !is_array($value['inline_edit'])) {
            return;
        }

        if (!array_key_exists('value', $value['inline_edit'])) {
            return;
        }

        $identifier = $value['inline_edit']['identifier'] ?? null;
        $propertyName = $value['inline_edit']['property'] ?? null;
        $propertyValue = $value['inline_edit']['value'] ?? null;

        if ((!is_string($identifier) && !is_int($identifier)) || $identifier === '') {
            return;
        }

        if (!is_string($propertyName) || $propertyName === '') {
            return;
        }

        if ($propertyValue !== null && !is_scalar($propertyValue)) {
            return;
        }

        $viewModel = $gridRepository->getComponent()->getViewModel();
        if (false === $viewModel instanceof GridViewModel || false === $viewModel->allowActions()) {
            return;
        }

        if ($propertyName === $gridRepository->getPrimaryKey()) {
            return;
        }

        $providerHandler = $gridRepository->getProviderHandler();
        $provider = $gridRepository->getProvider();

        $item = $providerHandler->getItem($provider, $identifier);
        if (empty($item->getData())) {
            return;
        }

        if (false === $this->isInlineEditable($viewModel, $item, $propertyName)) {
            return;
        }

        $item->setData($propertyName, $propertyValue);

        if (false === $this->isValid($viewModel, $item)) {
            return;
        }

        $providerHandler->saveItem($provider, $item);
    }

    private function isValid(GridViewModel $viewModel, DataObject $item): bool
    {
        $errorMessages = [];
        foreach ((array)$viewModel->getBlock()->getData('item_validators') as $itemValidator) {
            if (false === $itemValidator instanceof ItemValidatorInterface) {
                continue;
            }

            $validationResult = $itemValidator->validate($item);
            if ($validationResult !== true) {
                $errorMessages = array_merge($errorMessages, (array)$validationResult);
            }
        }

        foreach ($errorMessages as $errorMessage) {
            $this->messageManager->addErrorMessage((string)$errorMessage);
        }

        return empty($errorMessages);
    }

    private function isInlineEditable(GridViewModel $viewModel, DataObject $item, string $propertyName): bool
    {
        foreach ($viewModel->getColumns() as $column) {
            if ($column->getCode() !== $propertyName) {
                continue;
            }

            return $viewModel->getCellTemplate($item, $column) === self::INLINE_EDIT_TEMPLATE;
        }

        return false;
    }
}
