<?php declare(strict_types=1);

namespace Loki\AdminComponents\Test\Unit\Component\Form;

use Loki\AdminComponents\Component\Form\FormRepository;
use Loki\AdminComponents\Component\Form\FormViewModel;
use Loki\AdminComponents\Form\Item\ItemResolver;
use Magento\Framework\DataObject;
use Magento\Framework\View\Element\Template;
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

    public function testItemDataOnlyContainsFieldsAndPrimaryKey(): void
    {
        $item = new DataObject([
            'entity_id' => 42,
            'name' => 'Foo',
            'password_hash' => 'secret',
            'extra' => 'bar',
        ]);

        $itemResolver = $this->createStub(ItemResolver::class);
        $itemResolver->method('resolve')->willReturn($item);

        $repository = $this->createStub(FormRepository::class);
        $repository->method('getPrimaryKey')->willReturn('entity_id');

        $block = $this->createPartialMock(Template::class, []);
        $block->setData('additional_item_data', ['extra']);

        $viewModel = $this->createPartialMock(
            FormViewModel::class,
            ['getFieldsets', 'getRepository', 'getBlock']
        );
        $viewModel->method('getFieldsets')->willReturn([
            'base' => new Fieldset('base', 'Base', [new Field(null, ['code' => 'name'])]),
        ]);
        $viewModel->method('getRepository')->willReturn($repository);
        $viewModel->method('getBlock')->willReturn($block);

        $reflection = new \ReflectionProperty(FormViewModel::class, 'itemResolver');
        $reflection->setValue($viewModel, $itemResolver);

        $getItemData = new \ReflectionMethod(FormViewModel::class, 'getItemData');

        $this->assertSame(
            ['entity_id' => 42, 'name' => 'Foo', 'extra' => 'bar'],
            $getItemData->invoke($viewModel)
        );
    }
}
