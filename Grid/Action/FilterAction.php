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
        if (!isset($value['filters'])) {
            return;
        }

        if (!is_array($value['filters'])) {
            return;
        }

        /** @var GridViewModel $gridViewModel */
        $gridViewModel = $gridRepository->getComponent()->getViewModel();
        $gridFilters = $gridViewModel->getGridFilters();

        foreach ($value['filters'] as $filterName => $filterValue) {
            if (false === array_key_exists($filterName, $gridFilters)) {
                continue;
            }

            $gridFilter = $gridFilters[$filterName];
            if ($gridFilter->isEmpty($filterValue)) {
                continue;
            }

            $state = $this->stateManager->get($gridRepository->getNamespace());
            $state->setFilter(
                $filterName,
                $filterValue,
                $gridFilter->getConditionType(),
            );
        }
    }
}
