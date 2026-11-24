<?php declare(strict_types=1);

namespace Loki\AdminComponents\Grid\Action;

use Loki\AdminComponents\Component\Grid\GridRepository;
use Loki\AdminComponents\Component\Grid\GridViewModel;
use Loki\AdminComponents\Grid\StateManager;

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

        $sortBy = $value['sort']['sortBy'] ?? '';
        $sortDirection = $value['sort']['sortDirection'] ?? '';
        if (!is_string($sortBy) || !is_string($sortDirection)) {
            return;
        }

        $state = $this->stateManager->get($gridRepository->getNamespace());
        if (false === $this->isSortableColumn($gridRepository, $sortBy)) {
            $state->setSortBy('');
            return;
        }

        $state->setSortBy($sortBy);
        $state->setSortDirection($sortDirection);
    }

    private function isSortableColumn(GridRepository $gridRepository, string $sortBy): bool
    {
        $viewModel = $gridRepository->getComponent()->getViewModel();
        if (false === $viewModel instanceof GridViewModel) {
            return false;
        }

        return in_array($sortBy, $viewModel->getAvailableColumnCodes(), true);
    }
}
