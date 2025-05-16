<?php declare(strict_types=1);

namespace Yireo\LokiAdminComponents\Grid\Action;

use Yireo\LokiAdminComponents\Component\Grid\GridRepository;
use Yireo\LokiAdminComponents\Grid\StateManager;

class PageAction implements ActionInterface
{
    public function __construct(
        private StateManager $stateManager,
    ) {
    }

    public function execute(GridRepository $gridRepository, array $value): void
    {
        if (!isset($value['page'])) {
            return;
        }

        $state = $this->stateManager->get($gridRepository->getNamespace());
        $state->setPage((int)$value['page']);
    }
}
