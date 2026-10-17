<?php declare(strict_types=1);

namespace Loki\AdminComponents\Test\Unit\Ui\Button;

use Loki\AdminComponents\Test\Unit\Traits\CreateButtonFactory;
use Loki\AdminComponents\Ui\Button\Button;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ButtonFactoryTest extends TestCase
{
    use CreateButtonFactory;

    public function testCreate(): void
    {
        $subButton = $this->createButton('sub');
        $button = $this->createButtonFactory()->create(
            'id1',
            'fooAction',
            'Foo',
            'foo',
            'https://example.com/',
            [$subButton],
        );

        $this->assertInstanceOf(Button::class, $button);
        $this->assertSame('id1', $button->getId());
        $this->assertSame('fooAction', $button->getMethod());
        $this->assertSame('Foo', $button->getLabel());
        $this->assertSame('foo', $button->getCssClass());
        $this->assertSame('https://example.com/', $button->getUrl());
        $this->assertSame([$subButton], $button->getSubButtons());
    }

    public function testCreateWithEmptyIdFallsBackToMethod(): void
    {
        $button = $this->createButtonFactory()->create('', 'fooAction', 'Foo');
        $this->assertSame('fooAction', $button->getId());
    }

    #[DataProvider('getPrimaryCssClassData')]
    public function testCreateWithPrimary(string $cssClass, bool $primary, string $expectedCssClass): void
    {
        $button = $this->createButtonFactory()->create('id', 'fooAction', 'Foo', $cssClass, primary: $primary);
        $this->assertSame($expectedCssClass, $button->getCssClass());
    }

    public static function getPrimaryCssClassData(): array
    {
        return [
            ['', false, ''],
            ['', true, 'primary'],
            ['save', false, 'save'],
            ['save', true, 'save primary'],
        ];
    }

    public function testCreateLinkAction(): void
    {
        $factory = $this->createButtonFactory();
        $subButton = $this->createButton('sub');
        $button = $factory->createLinkAction('https://example.com/a', 'A', 'a', [$subButton], true);

        $this->assertSame('link:https://example.com/a', $button->getId());
        $this->assertSame('redirectAction', $button->getMethod());
        $this->assertSame('A', $button->getLabel());
        $this->assertSame('a primary', $button->getCssClass());
        $this->assertSame('https://example.com/a', $button->getUrl());
        $this->assertSame([$subButton], $button->getSubButtons());
    }

    public function testCreateLinkActionsWithDifferentUrlsHaveDifferentIds(): void
    {
        $factory = $this->createButtonFactory();
        $buttonA = $factory->createLinkAction('https://example.com/a', 'A');
        $buttonB = $factory->createLinkAction('https://example.com/b', 'B');

        $this->assertNotSame($buttonA->getId(), $buttonB->getId());
    }

    public function testCreateLinkActionWithExplicitId(): void
    {
        $button = $this->createButtonFactory()->createLinkAction('https://example.com/a', 'A', id: 'custom');
        $this->assertSame('custom', $button->getId());
    }

    #[DataProvider('getPredefinedActionData')]
    public function testPredefinedActions(
        string $factoryMethod,
        string $expectedId,
        string $expectedMethod,
        string $expectedLabel,
        string $expectedCssClass,
        string $expectedCssClassWithInvertedPrimary,
    ): void {
        $factory = $this->createButtonFactory();
        $button = $factory->$factoryMethod();

        $this->assertSame($expectedId, $button->getId());
        $this->assertSame($expectedMethod, $button->getMethod());
        $this->assertSame($expectedLabel, $button->getLabel());
        $this->assertSame($expectedCssClass, $button->getCssClass());
        $this->assertSame('', $button->getUrl());
        $this->assertSame([], $button->getSubButtons());

        $isPrimary = str_contains($expectedCssClass, 'primary');
        $button = $factory->$factoryMethod(primary: !$isPrimary);
        $this->assertSame($expectedCssClassWithInvertedPrimary, $button->getCssClass());
    }

    public static function getPredefinedActionData(): array
    {
        return [
            ['createNewAction', 'new', 'newAction', 'New', 'new primary', 'new'],
            ['createCloseAction', 'close', 'closeAction', 'Back', 'back', 'back primary'],
            ['createDeleteAction', 'delete', 'deleteAction', 'Delete', 'delete', 'delete primary'],
            ['createSaveContinueAction', 'saveAndContinue', 'saveAndContinueAction', 'Save & Continue', 'save', 'save primary'],
            ['createSaveDuplicateAction', 'saveAndDuplicate', 'saveAndDuplicateAction', 'Save & Duplicate', 'save', 'save primary'],
            ['createSaveCloseAction', 'saveAndClose', 'saveAndCloseAction', 'Save & Close', 'save primary', 'save'],
        ];
    }

    public function testCreateNewActionWithCustomLabel(): void
    {
        $button = $this->createButtonFactory()->createNewAction(label: 'New Attribute');
        $this->assertSame('New Attribute', $button->getLabel());
    }
}
