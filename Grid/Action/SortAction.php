<?php declare(strict_types=1);

namespace Yireo\LokiAdminComponents\Grid\Action;

use Yireo\LokiAdminComponents\Component\Grid\GridRepository;
use Yireo\LokiAdminComponents\Grid\State;
use Yireo\LokiAdminComponents\Grid\StateManager;

class SortAction implements ActionInterface
{
    public function __construct(
        private StateManager $stateManager,
    ) {
    }

    public function execute(GridRepository $gridRepository, array $value): void
    {
        if (!isset($value['sort'])) {
            return;
        }

        if (!is_array($value['sort'])) {
            return;
        }

        $state = $this->stateManager->get($gridRepository->getNamespace());
        $state->setSortBy((string)$value['sort']['sortBy']);
        $state->setSortDirection((string)$value['sort']['sortDirection']);
    }
}
