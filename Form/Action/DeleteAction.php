<?php declare(strict_types=1);

namespace Loki\AdminComponents\Form\Action;

use Loki\AdminComponents\Exception\FormValidationException;
use Magento\Framework\Message\Manager;
use Loki\AdminComponents\Component\Form\FormRepository;

class DeleteAction implements ActionInterface
{
    public function __construct(
        private Manager $messageManager
    ) {
    }

    public function execute(FormRepository $formRepository, array $value): void
    {
        $provider = $formRepository->getProvider();
        $providerHandler = $formRepository->getProviderHandler();

        $item = $formRepository->getItemFromData($value);

        $errorMessages = [];
        foreach ($formRepository->getItemDeleteValidators() as $itemDeleteValidator) {
            $validationResult = $itemDeleteValidator->validateDelete($item);
            if ($validationResult !== true) {
                $errorMessages = array_merge($errorMessages, $validationResult);
            }
        }

        if (!empty($errorMessages)) {
            foreach ($errorMessages as $errorMessage) {
                $this->messageManager->addErrorMessage($errorMessage);
            }

            throw new FormValidationException();
        }

        $providerHandler->deleteItem($provider, $item);

        $this->messageManager->addWarningMessage('Item deleted');
    }
}
