<?php declare(strict_types=1);

namespace Loki\AdminComponents\Grid\Action;

use Loki\AdminComponents\Component\Grid\GridRepository;
use Loki\AdminComponents\Component\Grid\GridViewModel;
use Loki\AdminComponents\Grid\StateManager;

class ActiveColumnsAction implements ActionInterface
{
    public function __construct(
        private StateManager $stateManager,
    ) {
    }

    public function execute(GridRepository $gridRepository, array $value): void
    {
        if (!isset($value['activeColumns'])) {
            return;
        }

        if (!is_array($value['activeColumns'])) {
            return;
        }

        $state = $this->stateManager->get($gridRepository->getNamespace());
        $state->setActiveColumns($value['activeColumns']);
    }
}
