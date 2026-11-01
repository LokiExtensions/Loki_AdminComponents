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

        $this->getRepository(['inline_edit' => $inlineEdit, 'search' => $search], false)
            ->saveValue(['inline_edit' => ['identifier' => 1, 'property' => 'name', 'value' => 'x']]);
    }

    public function testInlineEditIsExecutedWithSavePermission(): void
    {
        $inlineEdit = $this->createMock(ActionInterface::class);
        $inlineEdit->expects($this->once())->method('execute');

        $this->getRepository(['inline_edit' => $inlineEdit], true)
            ->saveValue(['inline_edit' => ['identifier' => 1, 'property' => 'name', 'value' => 'x']]);
    }

    private function getRepository(array $actions, bool $isAllowed): GridRepository
    {
        $authorization = $this->createStub(AuthorizationInterface::class);
        $authorization->method('isAllowed')->willReturn($isAllowed);

        $repository = new GridRepository(
            $this->createStub(StateManager::class),
            $this->createStub(ProviderHandlerListing::class),
            $this->createStub(ObjectManagerInterface::class),
            new ActionListing($actions),
            new ActionAuthorization($authorization)
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
