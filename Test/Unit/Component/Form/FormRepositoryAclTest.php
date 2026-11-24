<?php declare(strict_types=1);

namespace Loki\AdminComponents\Test\Unit\Component\Form;

use Loki\AdminComponents\Acl\ActionAuthorization;
use Loki\AdminComponents\Component\Form\FormRepository;
use Loki\AdminComponents\Form\Action\ActionInterface;
use Loki\AdminComponents\Form\Action\ActionListing;
use Loki\AdminComponents\ProviderHandler\ProviderHandlerListing;
use Loki\AdminComponents\Util\RedirectUrlValidator;
use Loki\Components\Exception\RedirectException;
use Loki\Components\Component\ComponentInterface;
use Loki\Components\Component\ComponentViewModelInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Element\Template;
use PHPUnit\Framework\TestCase;

class FormRepositoryAclTest extends TestCase
{
    public function testDisallowedActionIsNotExecuted(): void
    {
        $deleteAction = $this->createMock(ActionInterface::class);
        $deleteAction->expects($this->never())->method('execute');

        $messageManager = $this->createMock(ManagerInterface::class);
        $messageManager->expects($this->once())->method('addErrorMessage');

        $repository = $this->getRepository(['delete' => $deleteAction], false, $messageManager);
        $repository->saveValue(['actions' => ['delete'], 'item' => ['id' => 1]]);
    }

    public function testAllowedActionIsExecuted(): void
    {
        $deleteAction = $this->createMock(ActionInterface::class);
        $deleteAction->expects($this->once())->method('execute');

        $messageManager = $this->createMock(ManagerInterface::class);
        $messageManager->expects($this->never())->method('addErrorMessage');

        $repository = $this->getRepository(['delete' => $deleteAction], true, $messageManager);
        $repository->saveValue(['actions' => ['delete'], 'item' => ['id' => 1]]);
    }

    public function testValidRedirectIsThrown(): void
    {
        $repository = $this->getRepository(
            ['save' => $this->createStub(ActionInterface::class)],
            true,
            $this->createStub(ManagerInterface::class),
            true
        );

        $this->expectException(RedirectException::class);
        $repository->saveValue(['actions' => ['save'], 'redirect' => 'https://admin.example.com/admin/']);
    }

    public function testInvalidRedirectIsIgnored(): void
    {
        $repository = $this->getRepository(
            ['save' => $this->createStub(ActionInterface::class)],
            true,
            $this->createStub(ManagerInterface::class),
            false
        );

        $repository->saveValue(['actions' => ['save'], 'redirect' => 'https://evil.example/']);
        $this->addToAssertionCount(1);
    }

    private function getRepository(
        array $actions,
        bool $isAllowed,
        ManagerInterface $messageManager,
        bool $isValidRedirect = true
    ): FormRepository {
        $authorization = $this->createStub(AuthorizationInterface::class);
        $authorization->method('isAllowed')->willReturn($isAllowed);

        $redirectUrlValidator = $this->createStub(RedirectUrlValidator::class);
        $redirectUrlValidator->method('isValid')->willReturn($isValidRedirect);

        $repository = new FormRepository(
            $this->createStub(RequestInterface::class),
            $this->createStub(ProviderHandlerListing::class),
            $this->createStub(ObjectManagerInterface::class),
            new ActionListing($actions),
            new ActionAuthorization($authorization),
            $messageManager,
            $redirectUrlValidator
        );

        $block = $this->createPartialMock(Template::class, []);
        $block->setData('delete_acl_resource', 'Vendor_Module::delete');

        $viewModel = $this->createStub(ComponentViewModelInterface::class);
        $viewModel->method('getBlock')->willReturn($block);

        $component = $this->createStub(ComponentInterface::class);
        $component->method('getViewModel')->willReturn($viewModel);
        $repository->setComponent($component);

        return $repository;
    }
}
