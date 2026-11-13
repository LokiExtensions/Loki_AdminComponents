<?php declare(strict_types=1);

namespace Loki\AdminComponents\Test\Unit\Component\Grid;

use Loki\AdminComponents\Acl\ActionAuthorization;
use Loki\AdminComponents\Component\Grid\GridRepository;
use Loki\AdminComponents\Grid\Action\ActionInterface;
use Loki\AdminComponents\Grid\Action\ActionListing;
use Loki\AdminComponents\Grid\StateManager;
use Loki\AdminComponents\ProviderHandler\ProviderHandlerListing;
use Loki\Components\Component\ComponentInterface;
use Loki\Components\Component\ComponentViewModelInterface;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Element\Template;
use PHPUnit\Framework\TestCase;

class GridRepositoryAclTest extends TestCase
{
    public function testInlineEditIsSkippedWithoutSavePermission(): void
    {
        $inlineEdit = $this->createMock(ActionInterface::class);
        $inlineEdit->expects($this->never())->method('execute');
        $search = $this->createMock(ActionInterface::class);
        $search->expects($this->once())->method('execute');

        $messageManager = $this->createMock(MessageManagerInterface::class);
        $messageManager->expects($this->once())->method('addErrorMessage')
            ->with($this->callback(fn ($message) => (string)$message === 'You are not allowed to perform the action "inline_edit".'));

        $this->getRepository(['inline_edit' => $inlineEdit, 'search' => $search], false, $messageManager)
            ->saveValue(['inline_edit' => ['identifier' => 1, 'property' => 'name', 'value' => 'x']]);
    }

    public function testNoErrorWithoutSavePermissionWhenInlineEditIsNotRequested(): void
    {
        $inlineEdit = $this->createMock(ActionInterface::class);
        $inlineEdit->expects($this->never())->method('execute');
        $messageManager = $this->createMock(MessageManagerInterface::class);
        $messageManager->expects($this->never())->method('addErrorMessage');

        $this->getRepository(['inline_edit' => $inlineEdit], false, $messageManager)
            ->saveValue(['search' => 'foo']);
    }

    public function testInlineEditIsExecutedWithSavePermission(): void
    {
        $inlineEdit = $this->createMock(ActionInterface::class);
        $inlineEdit->expects($this->once())->method('execute');

        $this->getRepository(['inline_edit' => $inlineEdit], true)
            ->saveValue(['inline_edit' => ['identifier' => 1, 'property' => 'name', 'value' => 'x']]);
    }

    private function getRepository(
        array $actions,
        bool $isAllowed,
        ?MessageManagerInterface $messageManager = null
    ): GridRepository
    {
        $authorization = $this->createStub(AuthorizationInterface::class);
        $authorization->method('isAllowed')->willReturn($isAllowed);

        $repository = new GridRepository(
            $this->createStub(StateManager::class),
            $this->createStub(ProviderHandlerListing::class),
            $this->createStub(ObjectManagerInterface::class),
            new ActionListing($actions),
            new ActionAuthorization($authorization),
            $messageManager ?? $this->createStub(MessageManagerInterface::class)
        );

        $block = $this->createPartialMock(Template::class, []);
        $block->setData('save_acl_resource', 'Vendor_Module::save');

        $viewModel = $this->createStub(ComponentViewModelInterface::class);
        $viewModel->method('getBlock')->willReturn($block);

        $component = $this->createStub(ComponentInterface::class);
        $component->method('getViewModel')->willReturn($viewModel);
        $repository->setComponent($component);

        return $repository;
    }
}
