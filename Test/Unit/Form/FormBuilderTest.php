<?php declare(strict_types=1);

namespace Loki\AdminComponents\Test\Unit\Form;

use Loki\AdminComponents\Form\Field\FieldFactory;
use Loki\AdminComponents\Form\Fieldset\FieldsetFactory;
use Loki\AdminComponents\Form\Form;
use Loki\AdminComponents\Form\FormBuilder;
use Loki\AdminComponents\Form\FormFactory;
use Loki\AdminComponents\Test\Unit\Traits\CreateButtonFactory;
use Loki\AdminComponents\Ui\Button\Button;
use Magento\Framework\View\LayoutInterface;
use PHPUnit\Framework\TestCase;

class FormBuilderTest extends TestCase
{
    use CreateButtonFactory;

    /**
     * @see https://github.com/LokiExtensions/Loki_AdminComponents/issues/20
     */
    public function testCreateButtonWithPositionalArgumentsAsUsedByEmailTester2(): void
    {
        $button = $this->getFormBuilder()->createButton('send', 'Send Email', primary: true);

        $this->assertInstanceOf(Button::class, $button);
        $this->assertSame('send', $button->getId());
        $this->assertSame('send', $button->getMethod());
        $this->assertSame('Send Email', $button->getLabel());
        $this->assertSame('primary', $button->getCssClass());
        $this->assertSame('', $button->getUrl());
        $this->assertSame([], $button->getSubButtons());
    }

    public function testCreateButtonWithAllPositionalArguments(): void
    {
        $subButton = $this->createButton('sub');
        $button = $this->getFormBuilder()->createButton(
            'redirectAction',
            'Go',
            'go',
            'https://example.com/',
            [$subButton],
            true,
            'go_button'
        );

        $this->assertSame('go_button', $button->getId());
        $this->assertSame('redirectAction', $button->getMethod());
        $this->assertSame('Go', $button->getLabel());
        $this->assertSame('go primary', $button->getCssClass());
        $this->assertSame('https://example.com/', $button->getUrl());
        $this->assertSame([$subButton], $button->getSubButtons());
    }

    public function testCreateButtonWithoutPrimary(): void
    {
        $button = $this->getFormBuilder()->createButton('preview', 'Preview Email', 'preview');

        $this->assertSame('preview', $button->getId());
        $this->assertSame('preview', $button->getCssClass());
    }

    public function testButtonsWithDifferentMethodsDoNotOverwriteEachOtherInForm(): void
    {
        $formBuilder = $this->getFormBuilder();
        $send = $formBuilder->createButton('send', 'Send Email', primary: true);
        $preview = $formBuilder->createButton('preview', 'Preview Email', primary: true);

        $form = new Form(
            $this->createMock(FieldFactory::class),
            $this->createMock(FieldsetFactory::class),
            $this->createButtonFactory(),
        );
        $form->addButton($send)->addButton($preview);

        $this->assertSame(['send' => $send, 'preview' => $preview], $form->getButtons());
    }

    private function getFormBuilder(): FormBuilder
    {
        return new FormBuilder(
            $this->createMock(FormFactory::class),
            $this->createMock(FieldFactory::class),
            $this->createMock(FieldsetFactory::class),
            $this->createButtonFactory(),
            $this->createMock(LayoutInterface::class),
        );
    }
}
