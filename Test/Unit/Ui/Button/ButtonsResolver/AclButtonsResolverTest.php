<?php declare(strict_types=1);

namespace Loki\AdminComponents\Test\Unit\Ui\Button\ButtonsResolver;

use Loki\AdminComponents\Acl\ActionAuthorization;
use Loki\AdminComponents\Component\Form\FormRepository;
use Loki\AdminComponents\Ui\Button\Button;
use Loki\AdminComponents\Ui\Button\ButtonsResolver\AclButtonsResolver;
use Loki\Components\Component\ComponentViewModelInterface;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\View\Element\Template;
use PHPUnit\Framework\TestCase;

class AclButtonsResolverTest extends TestCase
{
    public function testDisallowedButtonsAreRemoved(): void
    {
        $authorization = $this->createStub(AuthorizationInterface::class);
        $authorization->method('isAllowed')->willReturnCallback(
            fn (string $resource) => $resource !== 'Vendor_Module::delete'
        );

        $block = $this->createPartialMock(Template::class, []);
        $block->setData([
            'save_acl_resource' => 'Vendor_Module::save',
            'delete_acl_resource' => 'Vendor_Module::delete',
        ]);

        $viewModel = $this->createStub(ComponentViewModelInterface::class);
        $viewModel->method('getBlock')->willReturn($block);
        $repository = $this->createStub(FormRepository::class);
        $repository->method('getViewModel')->willReturn($viewModel);

        $close = new Button('close', 'closeAction', 'Back');
        $delete = new Button('delete', 'deleteAction', 'Delete');
        $save = new Button('saveAndClose', 'saveAndCloseAction', 'Save & Close');

        $buttons = (new AclButtonsResolver(new ActionAuthorization($authorization)))
            ->resolve($repository, [$close, $delete, $save]);

        $this->assertSame([$close, $save], array_values($buttons));
    }
}
