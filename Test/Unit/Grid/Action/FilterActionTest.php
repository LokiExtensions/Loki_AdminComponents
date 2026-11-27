<?php declare(strict_types=1);

namespace Loki\AdminComponents\Test\Unit\Grid\Action;

use Loki\AdminComponents\Component\Grid\GridRepository;
use Loki\AdminComponents\Component\Grid\GridViewModel;
use Loki\AdminComponents\Grid\Action\FilterAction;
use Loki\AdminComponents\Grid\Filter\Filter;
use Loki\AdminComponents\Grid\State;
use Loki\AdminComponents\Grid\StateManager;
use Loki\Components\Component\Component;
use PHPUnit\Framework\TestCase;

class FilterActionTest extends TestCase
{
    public function testClearedFilterIsRemovedFromState(): void
    {
        $state = $this->createMock(State::class);
        $state->expects($this->once())->method('removeFilter')->with('status');
        $state->expects($this->never())->method('setFilter');

        $this->execute($state, ['status' => '']);
    }

    public function testFilledFilterIsStored(): void
    {
        $state = $this->createMock(State::class);
        $state->expects($this->never())->method('removeFilter');
        $state->expects($this->once())->method('setFilter')->with('status', '0', 'eq');

        $this->execute($state, ['status' => '0']);
    }

    public function testIntegerValueIsStoredAsString(): void
    {
        $state = $this->createMock(State::class);
        $state->expects($this->once())->method('setFilter')->with('status', '1', 'eq');

        $this->execute($state, ['status' => 1]);
    }

    public function testArrayValueIsRejectedForScalarCondition(): void
    {
        $state = $this->createMock(State::class);
        $state->expects($this->never())->method('setFilter');
        $state->expects($this->never())->method('removeFilter');

        $this->execute($state, ['status' => ['regexp' => '(a+)+$']]);
    }

    public function testFromToValueOnlyKeepsFromAndTo(): void
    {
        $state = $this->createMock(State::class);
        $state->expects($this->once())->method('setFilter')
            ->with('created_at', ['from' => '2026-01-01', 'to' => ''], 'from_to');

        $this->execute($state, ['created_at' => ['from' => '2026-01-01', 'to' => ['x'], 'regexp' => 'a']], 'from_to');
    }

    public function testListValueRejectsNestedArrays(): void
    {
        $state = $this->createMock(State::class);
        $state->expects($this->once())->method('setFilter')->with('status', ['1', '2'], 'in');

        $this->execute($state, ['status' => [1, '2']], 'in');

        $state = $this->createMock(State::class);
        $state->expects($this->never())->method('setFilter');

        $this->execute($state, ['status' => [['nin' => 1]]], 'in');
    }

    private function execute(State $state, array $filters, string $conditionType = 'eq'): void
    {
        $filterCode = (string)array_key_first($filters);
        $filter = $this->createMock(Filter::class);
        $filter->method('isEmpty')->willReturnCallback(fn(mixed $value): bool => $value === '');
        $filter->method('getConditionType')->willReturn($conditionType);

        $viewModel = $this->createMock(GridViewModel::class);
        $viewModel->method('getGridFilters')->willReturn([$filterCode => $filter]);

        $component = $this->createMock(Component::class);
        $component->method('getViewModel')->willReturn($viewModel);

        $repository = $this->createMock(GridRepository::class);
        $repository->method('getComponent')->willReturn($component);
        $repository->method('getNamespace')->willReturn('ns');

        $stateManager = $this->createMock(StateManager::class);
        $stateManager->method('get')->willReturn($state);

        (new FilterAction($stateManager))->execute($repository, ['filters' => $filters]);
    }
}
