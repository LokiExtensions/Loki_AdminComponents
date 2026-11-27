<?php declare(strict_types=1);

namespace Loki\AdminComponents\Test\Unit\Component\Form;

use Loki\AdminComponents\Acl\ActionAuthorization;
use Loki\AdminComponents\Component\Form\FormRepository;
use Loki\AdminComponents\Exception\FormActionException;
use Loki\AdminComponents\Form\Action\ActionListing;
use Loki\AdminComponents\ProviderHandler\ProviderHandlerInterface;
use Loki\AdminComponents\ProviderHandler\ProviderHandlerListing;
use Loki\AdminComponents\Util\RedirectUrlValidator;
use Loki\Components\Component\ComponentInterface;
use Loki\Components\Component\ComponentViewModelInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Element\Template;
use PHPUnit\Framework\TestCase;

class FormRepositoryItemTest extends TestCase
{
    public function testItemMustMatchRequestedId(): void
    {
        $messageManager = $this->createMock(ManagerInterface::class);
        $messageManager->expects($this->once())->method('addErrorMessage');

        $this->expectException(FormActionException::class);
        $this->getRepository(5, $messageManager)->getItemFromData(['item' => ['id' => 6]]);
    }

    public function testItemMatchingRequestedIdIsLoaded(): void
    {
        $item = new DataObject(['id' => 5]);
        $repository = $this->getRepository(5, $this->createStub(ManagerInterface::class), $item);

        $this->assertSame($item, $repository->getItemFromData(['item' => ['id' => '5']]));
    }

    private function getRepository(
        int $requestedId,
        ManagerInterface $messageManager,
        ?DataObject $item = null
    ): FormRepository {
        $request = $this->createStub(RequestInterface::class);
        $request->method('getParam')->willReturnMap([['id', null, (string)$requestedId]]);

        $providerHandler = $this->createStub(ProviderHandlerInterface::class);
        $providerHandler->method('getItem')->willReturn($item ?? new DataObject());

        $providerHandlerListing = $this->createStub(ProviderHandlerListing::class);
        $providerHandlerListing->method('getByName')->willReturn($providerHandler);

        $repository = new FormRepository(
            $request,
            $providerHandlerListing,
            $this->createStub(ObjectManagerInterface::class),
            new ActionListing([]),
            $this->createStub(ActionAuthorization::class),
            $messageManager,
            $this->createStub(RedirectUrlValidator::class)
        );

        $block = $this->createPartialMock(Template::class, []);
        $block->setData('provider', new \stdClass());
        $block->setData('provider_handler', 'test');

        $viewModel = $this->createStub(ComponentViewModelInterface::class);
        $viewModel->method('getBlock')->willReturn($block);

        $component = $this->createStub(ComponentInterface::class);
        $component->method('getViewModel')->willReturn($viewModel);
        $repository->setComponent($component);

        return $repository;
    }
}
