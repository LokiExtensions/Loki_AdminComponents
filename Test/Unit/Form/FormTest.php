<?php declare(strict_types=1);

namespace Loki\AdminComponents\Test\Unit\Form;

use Loki\AdminComponents\Form\Field\FieldFactory;
use Loki\AdminComponents\Form\Fieldset\FieldsetFactory;
use Loki\AdminComponents\Form\Form;
use Loki\AdminComponents\Test\Unit\Traits\CreateButtonFactory;
use PHPUnit\Framework\TestCase;

class FormTest extends TestCase
{
    use CreateButtonFactory;

    public function testAddButtonKeysButtonsById(): void
    {
        $form = $this->getForm();
        $close = $this->createButton('close');
        $save = $this->createButton('save');

        $this->assertSame($form, $form->addButton($close));
        $form->addButton($save);

        $this->assertSame(['close' => $close, 'save' => $save], $form->getButtons());
    }

    public function testAddButtonWithSameIdReplacesButton(): void
    {
        $form = $this->getForm();
        $first = $this->createButton('save', 'saveAction', 'Save');
        $second = $this->createButton('save', 'saveAction', 'Store');

        $form->addButton($first)->addButton($second);

        $this->assertSame(['save' => $second], $form->getButtons());
    }

    private function getForm(): Form
    {
        return new Form(
            $this->createMock(FieldFactory::class),
            $this->createMock(FieldsetFactory::class),
            $this->createButtonFactory(),
        );
    }
}
