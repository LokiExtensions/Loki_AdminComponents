<?php declare(strict_types=1);

namespace Loki\AdminComponents\Test\Unit\Grid\Action;

use Loki\AdminComponents\Component\Grid\GridRepository;
use Loki\AdminComponents\Component\Grid\GridViewModel;
use Loki\AdminComponents\Grid\Action\ActiveColumnsAction;
use Loki\AdminComponents\Grid\State;
use Loki\AdminComponents\Grid\StateManager;
use Loki\Components\Component\Component;
use PHPUnit\Framework\TestCase;

class ActiveColumnsActionTest extends TestCase
{
    public function testAvailableColumnsArePassedToState(): void
    {
        $state = $this->createMock(State::class);
        $state->expects($this->once())
            ->method('setActiveColumns')
            ->with(['name'], ['name', 'email', 'status']);

        $this->execute($state, ['activeColumns' => ['name']], ['name', 'email', 'status']);
    }

    public function testMissingActiveColumnsIsIgnored(): void
    {
        $state = $this->createMock(State::class);
        $state->expects($this->never())->method('setActiveColumns');

        $this->execute($state, ['page' => 2], ['name'], false);
    }

    public function testInvalidActiveColumnsIsIgnored(): void
    {
        $state = $this->createMock(State::class);
        $state->expects($this->never())->method('setActiveColumns');

        $this->execute($state, ['activeColumns' => 'name'], ['name'], false);
    }

    private function execute(
        State $state,
        array $value,
        array $availableColumnCodes,
        bool $expectColumnCodes = true
    ): void {
        $viewModel = $this->createMock(GridViewModel::class);
        $viewModel->expects($expectColumnCodes ? $this->once() : $this->never())
            ->method('getAvailableColumnCodes')
            ->willReturn($availableColumnCodes);

        $component = $this->createMock(Component::class);
        $component->method('getViewModel')->willReturn($viewModel);

        $repository = $this->createMock(GridRepository::class);
        $repository->method('getComponent')->willReturn($component);
        $repository->method('getNamespace')->willReturn('ns');

        $stateManager = $this->createMock(StateManager::class);
        $stateManager->method('get')->willReturn($state);

        (new ActiveColumnsAction($stateManager))->execute($repository, $value);
    }
}
