<?php declare(strict_types=1);

namespace Yireo\LokiAdminComponents\Grid\Action;

use Yireo\LokiAdminComponents\Component\Grid\GridRepository;

class InlineEditAction implements ActionInterface
{
    public function execute(GridRepository $gridRepository, array $value): void
    {
        if (!isset($value['inline_edit'])) {
            return;
        }

        $identifier = $value['inline_edit']['identifier'];
        $propertyName = $value['inline_edit']['property'];
        $propertyValue = $value['inline_edit']['value'];

        $providerHandler = $gridRepository->getProviderHandler();
        $provider = $gridRepository->getProvider();

        $item = $providerHandler->getItem($provider, $identifier);
        $item->setData($propertyName, $propertyValue);
        $providerHandler->saveItem($provider, $item);
    }
}
