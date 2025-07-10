<?php declare(strict_types=1);

namespace Loki\AdminComponents\Grid\Action;

use Loki\AdminComponents\Component\Grid\GridRepository;
use Loki\AdminComponents\Grid\State;
use Loki\AdminComponents\Grid\StateManager;

class SearchAction implements ActionInterface
{
    public function __construct(
        private StateManager $stateManager,
    ) {
    }

    public function execute(GridRepository $gridRepository, array $value): void
    {
        if (!isset($value['search'])) {
            return;
        }

        $state = $this->stateManager->get($gridRepository->getNamespace());
        $state->setSearch(trim((string)$value['search']));
    }
}
