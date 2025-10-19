<?php declare(strict_types=1);

namespace Loki\AdminComponents\Grid\Action;

use Loki\AdminComponents\Component\Grid\GridRepository;
use Loki\AdminComponents\Component\Grid\GridViewModel;
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

        /** @var GridViewModel $gridViewModel */
        $gridViewModel = $gridRepository->getComponent()->getViewModel();
        $gridFilters = $gridViewModel->getGridFilters();

        if (false === array_key_exists($value['filter']['name'], $gridFilters)) {
            return;
        }

        $gridFilter = $gridFilters[$value['filter']['name']];

        $state = $this->stateManager->get($gridRepository->getNamespace());
        $state->setFilter(
            $gridFilter->getCode(),
            (string)$value['filter']['value'],
            $gridFilter->getConditionType(),
        );
    }
}
