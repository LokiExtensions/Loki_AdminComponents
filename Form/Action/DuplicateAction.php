<?php declare(strict_types=1);

namespace Yireo\LokiAdminComponents\Form\Action;

use Magento\Framework\Message\Manager;
use Yireo\LokiAdminComponents\Component\Form\FormRepository;

class DuplicateAction implements ActionInterface
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
        $providerHandler->duplicateItem($provider, $item);

        $this->messageManager->addSuccessMessage('Item has been duplicated');
    }
}
