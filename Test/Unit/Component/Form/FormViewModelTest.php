<?php declare(strict_types=1);

namespace Loki\AdminComponents\Test\Unit\Component\Form;

use Loki\AdminComponents\Component\Form\FormViewModel;
use Loki\AdminComponents\Form\Field\Field;
use Loki\AdminComponents\Form\Fieldset\Fieldset;
use PHPUnit\Framework\TestCase;

class FormViewModelTest extends TestCase
{
    public function testGetFieldsReturnsFieldsOfAllFieldsets(): void
    {
        $name = new Field(null, ['code' => 'name']);
        $content = new Field(null, ['code' => 'content', 'field_type' => 'editor']);
        $email = new Field(null, ['code' => 'email']);

        $viewModel = $this->createPartialMock(FormViewModel::class, ['getFieldsets']);
        $viewModel->method('getFieldsets')->willReturn([
            'base' => new Fieldset('base', 'Base', [$name, $content]),
            'contact' => new Fieldset('contact', 'Contact', [$email]),
        ]);

        $this->assertSame(
            ['name' => $name, 'content' => $content, 'email' => $email],
            $viewModel->getFields()
        );
    }
}
