<?php declare(strict_types=1);

namespace Loki\AdminComponents\Test\Unit\Form;

use Loki\AdminComponents\Form\Field\Field;
use Loki\AdminComponents\Form\Field\FieldFactory;
use Loki\AdminComponents\Form\Fieldset\Fieldset;
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

    public function testAddFieldKeysFieldsByCode(): void
    {
        $form = $this->getForm();
        $email = new Field(null, ['code' => 'email']);
        $name = new Field(null, ['code' => 'name']);

        $this->assertSame($form, $form->addField($email));
        $form->addField($name);

        $this->assertSame(['email' => $email, 'name' => $name], $form->getFields());
    }

    public function testAddFieldsetKeysFieldsetsByCode(): void
    {
        $form = $this->getForm();
        $base = new Fieldset('base', 'Generic Settings');
        $extra = new Fieldset('extra', 'Extra');

        $this->assertSame($form, $form->addFieldset($base));
        $form->addFieldset($extra);

        $this->assertSame(['base' => $base, 'extra' => $extra], $form->getFieldsets());
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
