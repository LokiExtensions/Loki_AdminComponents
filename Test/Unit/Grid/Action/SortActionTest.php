<?php declare(strict_types=1);

namespace Loki\AdminComponents\Test\Unit\Grid\Action;

use Loki\AdminComponents\Component\Grid\GridRepository;
use Loki\AdminComponents\Component\Grid\GridViewModel;
use Loki\AdminComponents\Grid\Action\SortAction;
use Loki\AdminComponents\Grid\State;
use Loki\AdminComponents\Grid\StateManager;
use Loki\Components\Component\Component;
use PHPUnit\Framework\TestCase;

class SortActionTest extends TestCase
{
    public function testKnownColumnIsStored(): void
    {
        $state = $this->createMock(State::class);
        $state->expects($this->once())->method('setSortBy')->with('entity_type_code');
        $state->expects($this->once())->method('setSortDirection')->with('ASC');

        $this->execute($state, ['sortBy' => 'entity_type_code', 'sortDirection' => 'ASC']);
    }

    public function testUnknownColumnClearsSorting(): void
    {
        $state = $this->createMock(State::class);
        $state->expects($this->once())->method('setSortBy')->with('');
        $state->expects($this->never())->method('setSortDirection');

        $this->execute($state, ['sortBy' => '(SELECT SLEEP(3))', 'sortDirection' => 'ASC']);
    }

    public function testNonStringValuesAreIgnored(): void
    {
        $state = $this->createMock(State::class);
        $state->expects($this->never())->method('setSortBy');
        $state->expects($this->never())->method('setSortDirection');

        $this->execute($state, ['sortBy' => ['entity_type_code'], 'sortDirection' => 'ASC']);
    }

    private function execute(State $state, array $sort): void
    {
        $viewModel = $this->createMock(GridViewModel::class);
        $viewModel->method('getAvailableColumnCodes')->willReturn(['entity_type_id', 'entity_type_code']);

        $component = $this->createMock(Component::class);
        $component->method('getViewModel')->willReturn($viewModel);

        $repository = $this->createMock(GridRepository::class);
        $repository->method('getComponent')->willReturn($component);
        $repository->method('getNamespace')->willReturn('ns');

        $stateManager = $this->createMock(StateManager::class);
        $stateManager->method('get')->willReturn($state);

        (new SortAction($stateManager))->execute($repository, ['sort' => $sort]);
    }
}
