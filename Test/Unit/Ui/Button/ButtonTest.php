<?php declare(strict_types=1);

namespace Loki\AdminComponents\Test\Unit\Ui\Button;

use Loki\AdminComponents\Ui\Button\Button;
use Loki\AdminComponents\Ui\Button\ButtonInterface;
use PHPUnit\Framework\TestCase;

class ButtonTest extends TestCase
{
    public function testDefaults(): void
    {
        $button = new Button('id', 'fooAction', 'Foo');

        $this->assertInstanceOf(ButtonInterface::class, $button);
        $this->assertSame('id', $button->getId());
        $this->assertSame('fooAction', $button->getMethod());
        $this->assertSame('Foo', $button->getLabel());
        $this->assertSame('', $button->getCssClass());
        $this->assertSame('', $button->getUrl());
        $this->assertSame([], $button->getSubButtons());
    }

    public function testSetters(): void
    {
        $subButton = new Button('sub', 'subAction', 'Sub');
        $button = new Button('id', 'fooAction', 'Foo');

        $button->setId('id2');
        $button->setMethod('barAction');
        $button->setLabel('Bar');
        $button->setCssClass('bar');
        $button->setUrl('https://example.com/');
        $button->setSubButtons([$subButton]);

        $this->assertSame('id2', $button->getId());
        $this->assertSame('barAction', $button->getMethod());
        $this->assertSame('Bar', $button->getLabel());
        $this->assertSame('bar', $button->getCssClass());
        $this->assertSame('https://example.com/', $button->getUrl());
        $this->assertSame([$subButton], $button->getSubButtons());
    }
}
