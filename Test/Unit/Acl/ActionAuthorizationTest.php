<?php declare(strict_types=1);

namespace Loki\AdminComponents\Test\Unit\Acl;

use Loki\AdminComponents\Acl\ActionAuthorization;
use Loki\AdminComponents\Ui\Button\Button;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\View\Element\Template;
use PHPUnit\Framework\TestCase;

class ActionAuthorizationTest extends TestCase
{
    public function testActionWithoutAclResourceIsAllowed(): void
    {
        $authorization = $this->getAuthorization([]);

        $this->assertTrue($authorization->isAllowed($this->getBlock([]), 'save'));
        $this->assertTrue($authorization->isAllowed($this->getBlock([]), 'delete'));
    }

    public function testSaveActionsUseSaveAclResource(): void
    {
        $authorization = $this->getAuthorization(['Vendor_Module::save' => false]);
        $block = $this->getBlock(['save_acl_resource' => 'Vendor_Module::save']);

        foreach (['save', 'duplicate', 'inline_edit'] as $action) {
            $this->assertFalse($authorization->isAllowed($block, $action), $action);
        }

        $this->assertTrue($authorization->isAllowed($block, 'delete'));
        $this->assertTrue($authorization->isAllowed($block, 'search'));
    }

    public function testDeleteActionUsesDeleteAclResource(): void
    {
        $authorization = $this->getAuthorization(['Vendor_Module::delete' => false, 'Vendor_Module::save' => true]);
        $block = $this->getBlock([
            'save_acl_resource' => 'Vendor_Module::save',
            'delete_acl_resource' => 'Vendor_Module::delete',
        ]);

        $this->assertFalse($authorization->isAllowed($block, 'delete'));
        $this->assertTrue($authorization->isAllowed($block, 'save'));
    }

    public function testButtonsFollowTheirAction(): void
    {
        $authorization = $this->getAuthorization(['Vendor_Module::delete' => false]);
        $block = $this->getBlock(['delete_acl_resource' => 'Vendor_Module::delete']);

        $this->assertFalse($authorization->isButtonAllowed($block, $this->getButton('delete')));
        $this->assertTrue($authorization->isButtonAllowed($block, $this->getButton('saveAndClose')));
        $this->assertTrue($authorization->isButtonAllowed($block, $this->getButton('close')));
    }

    private function getAuthorization(array $permissions): ActionAuthorization
    {
        $authorization = $this->createStub(AuthorizationInterface::class);
        $authorization->method('isAllowed')->willReturnCallback(
            fn (string $resource) => $permissions[$resource] ?? true
        );

        return new ActionAuthorization($authorization);
    }

    private function getBlock(array $data): Template
    {
        $block = $this->createPartialMock(Template::class, []);
        $block->setData($data);

        return $block;
    }

    private function getButton(string $id): Button
    {
        return new Button($id, $id . 'Action', $id);
    }
}
