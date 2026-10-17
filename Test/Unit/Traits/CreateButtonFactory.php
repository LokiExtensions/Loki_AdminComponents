<?php declare(strict_types=1);

namespace Loki\AdminComponents\Test\Unit\Traits;

use Loki\AdminComponents\Ui\Button\Button;
use Loki\AdminComponents\Ui\Button\ButtonFactory;
use Magento\Framework\ObjectManagerInterface;

trait CreateButtonFactory
{
    protected function createButtonFactory(): ButtonFactory
    {
        $objectManager = $this->createMock(ObjectManagerInterface::class);
        $objectManager->method('create')->willReturnCallback(
            fn (string $className, array $arguments) => new $className(...$arguments)
        );

        return new ButtonFactory($objectManager);
    }

    protected function createButton(string $id, string $method = 'fooAction', string $label = 'Foo'): Button
    {
        return new Button($id, $method, $label);
    }
}
