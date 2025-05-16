<?php declare(strict_types=1);

namespace Yireo\LokiAdminComponents\Grid\Action;

use Yireo\LokiAdminComponents\Component\Grid\GridRepository;
use Yireo\LokiAdminComponents\Grid\State;
use Yireo\LokiAdminComponents\Grid\StateManager;

class LimitAction implements ActionInterface
{
    public function __construct(
        private StateManager $stateManager,
    ) {
    }

    public function execute(GridRepository $gridRepository, array $value): void
    {
        if (!isset($value['limit'])) {
            return;
        }

        $state = $this->stateManager->get($gridRepository->getNamespace());
        $state->setLimit((int)$value['limit']);
    }
}
