<?php declare(strict_types=1);

namespace Yireo\LokiAdminComponents\Component\Form\FormRepository;

use Magento\Framework\Message\Manager;
use Yireo\LokiAdminComponents\Component\Form\FormRepository;

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
        $providerHandler->deleteItem($provider, $item);

        $this->messageManager->addWarningMessage('Item deleted');
    }
}
