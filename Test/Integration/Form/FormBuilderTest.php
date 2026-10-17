<?php declare(strict_types=1);

namespace Loki\AdminComponents\Test\Integration\Form;

use Loki\AdminComponents\Form\FormBuilder;
use Loki\AdminComponents\Ui\Button\Button;
use Magento\Framework\App\ObjectManager;
use Magento\TestFramework\Fixture\AppArea;
use PHPUnit\Framework\TestCase;

#[AppArea('adminhtml')]
class FormBuilderTest extends TestCase
{
    /**
     * @see https://github.com/LokiExtensions/Loki_AdminComponents/issues/20
     */
    public function testCreateFormWithButtonsAsUsedByEmailTester2(): void
    {
        $formBuilder = ObjectManager::getInstance()->get(FormBuilder::class);

        $form = $formBuilder->createForm('emailtester2_form')
            ->addButton($formBuilder->createButton('send', 'Send Email', primary: true))
            ->addButton($formBuilder->createButton('preview', 'Preview Email', primary: true));

        $buttons = $form->getButtons();
        $this->assertSame(['send', 'preview'], array_keys($buttons));

        $this->assertInstanceOf(Button::class, $buttons['send']);
        $this->assertSame('send', $buttons['send']->getMethod());
        $this->assertSame('Send Email', $buttons['send']->getLabel());
        $this->assertSame('primary', $buttons['send']->getCssClass());
        $this->assertSame('', $buttons['send']->getUrl());

        $this->assertSame('preview', $buttons['preview']->getMethod());
        $this->assertSame('Preview Email', $buttons['preview']->getLabel());
    }
}
