<?php declare(strict_types=1);

namespace Loki\AdminComponents\Test\Unit\Grid\Cell;

use Loki\AdminComponents\Grid\Cell\CellAction;
use Loki\AdminComponents\Grid\Cell\CellActionFactory;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\UrlFactory;
use Magento\Framework\UrlInterface;
use PHPUnit\Framework\TestCase;

class CellActionFactoryTest extends TestCase
{
    public function testPostActionWithConfirmation(): void
    {
        $action = $this->getFactory()->createFromData([
            'id' => 5,
            'label' => 'Delete',
            'url' => 'vendor/entity/delete',
            'post' => true,
            'confirm' => 'Are you sure?',
        ]);

        $this->assertTrue($action->isPost());
        $this->assertSame('Are you sure?', $action->getConfirm());
        $this->assertSame('https://admin.example.com/vendor/entity/delete/id/5/', $action->getUrl());
    }

    public function testActionIsNoPostByDefault(): void
    {
        $action = $this->getFactory()->createFromData(['label' => 'Open', 'url' => 'vendor/entity/open']);

        $this->assertFalse($action->isPost());
        $this->assertSame('', $action->getConfirm());
    }

    private function getFactory(): CellActionFactory
    {
        $url = $this->createStub(UrlInterface::class);
        $url->method('getUrl')->willReturnCallback(
            fn (string $path, array $params) => 'https://admin.example.com/' . $path
                . (isset($params['id']) ? '/id/' . $params['id'] : '') . '/'
        );

        $urlFactory = $this->createStub(UrlFactory::class);
        $urlFactory->method('create')->willReturn($url);

        $objectManager = $this->createStub(ObjectManagerInterface::class);
        $objectManager->method('create')->willReturnCallback(
            fn (string $class, array $arguments) => new CellAction(...$arguments)
        );

        return new CellActionFactory($objectManager, $urlFactory);
    }
}
