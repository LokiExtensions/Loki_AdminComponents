<?php declare(strict_types=1);

namespace Loki\AdminComponents\Grid\Action;

use Loki\AdminComponents\Component\Grid\GridRepository;
use Loki\AdminComponents\Grid\StateManager;

class RemoveFilterAction implements ActionInterface
{
    public function __construct(
        private StateManager $stateManager,
    ) {
    }

    public function execute(GridRepository $gridRepository, array $value): void
    {
        if (!isset($value['removeFilter'])) {
            return;
        }

        $state = $this->stateManager->get($gridRepository->getNamespace());
        $currentFilters = $state->getFilters();

        if (isset($currentFilters[$value['removeFilter']])) {
            unset($currentFilters[$value['removeFilter']]);
        }

        $state->setFilters($currentFilters);
    }
}
