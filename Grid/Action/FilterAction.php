<?php declare(strict_types=1);

namespace Loki\AdminComponents\Grid\Action;

use Loki\AdminComponents\Component\Grid\GridRepository;
use Loki\AdminComponents\Component\Grid\GridViewModel;
use Loki\AdminComponents\Grid\StateManager;

class FilterAction implements ActionInterface
{
    private const LIST_CONDITION_TYPES = ['in', 'nin'];

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
            $filterValue = $this->normalizeValue($filterValue, $gridFilter->getConditionType());
            if ($filterValue === false) {
                continue;
            }

            $state = $this->stateManager->get($gridRepository->getNamespace());
            if ($gridFilter->isEmpty($filterValue)) {
                $state->removeFilter($filterName);
                continue;
            }

            $state->setFilter(
                $filterName,
                $filterValue,
                $gridFilter->getConditionType(),
            );
        }
    }

    private function normalizeValue(mixed $value, string $conditionType): string|array|false
    {
        if ($conditionType === 'from_to') {
            if (false === is_array($value)) {
                return false;
            }

            return [
                'from' => $this->normalizeScalar($value['from'] ?? ''),
                'to' => $this->normalizeScalar($value['to'] ?? ''),
            ];
        }

        if (in_array($conditionType, self::LIST_CONDITION_TYPES, true)) {
            if ($value === null || $value === '') {
                return '';
            }

            $values = is_array($value) ? array_values($value) : [$value];
            foreach ($values as $listValue) {
                if (false === is_scalar($listValue)) {
                    return false;
                }
            }

            return array_map(fn ($listValue) => (string)$listValue, $values);
        }

        if ($value === null) {
            return '';
        }

        if (false === is_scalar($value)) {
            return false;
        }

        return (string)$value;
    }

    private function normalizeScalar(mixed $value): string
    {
        return is_scalar($value) ? (string)$value : '';
    }
}
