<?php declare(strict_types=1);

namespace Loki\AdminComponents\Grid\Action;

use Loki\AdminComponents\Component\Grid\GridRepository;
use Loki\AdminComponents\Grid\StateManager;

class FilterAction implements ActionInterface
{
    public function __construct(
        private StateManager $stateManager,
    ) {
    }

    public function execute(GridRepository $gridRepository, array $value): void
    {
        if (!isset($value['filter'])) {
            return;
        }

        if (!is_array($value['filter'])) {
            return;
        }

        $state = $this->stateManager->get($gridRepository->getNamespace());
        $state->setFilter(
            (string)$value['filter']['name'],
            (string)$value['filter']['value'],
        );
    }
}
