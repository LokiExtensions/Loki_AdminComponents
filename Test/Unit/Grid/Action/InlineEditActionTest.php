<?php declare(strict_types=1);

namespace Loki\AdminComponents\Test\Unit\Grid\Action;

use Loki\AdminComponents\Component\Grid\GridRepository;
use Loki\AdminComponents\Component\Grid\GridViewModel;
use Loki\AdminComponents\Grid\Action\InlineEditAction;
use Loki\AdminComponents\Grid\Column\Column;
use Loki\AdminComponents\ProviderHandler\ProviderHandlerInterface;
use Loki\Components\Component\Component;
use Magento\Framework\DataObject;
use PHPUnit\Framework\TestCase;

class InlineEditActionTest extends TestCase
{
    public function testInlineEditableColumnIsSaved(): void
    {
        $item = new DataObject(['id' => 4, 'name' => 'old']);
        $providerHandler = $this->getProviderHandler($item, true);

        $this->execute($providerHandler, ['identifier' => '4', 'property' => 'name', 'value' => 'new']);

        $this->assertSame('new', $item->getData('name'));
    }

    public function testColumnWithoutInlineEditTemplateIsNotSaved(): void
    {
        $item = new DataObject(['id' => 4, 'entity_model' => 'Foo']);
        $providerHandler = $this->getProviderHandler($item, false);

        $this->execute($providerHandler, ['identifier' => 4, 'property' => 'entity_model', 'value' => 'Bar']);

        $this->assertSame('Foo', $item->getData('entity_model'));
    }

    public function testUnknownColumnIsNotSaved(): void
    {
        $item = new DataObject(['id' => 4]);
        $this->execute($this->getProviderHandler($item, false), ['identifier' => 4, 'property' => 'foo', 'value' => 'x']);

        $this->assertFalse($item->hasData('foo'));
    }

    public function testPrimaryKeyIsNotSaved(): void
    {
        $providerHandler = $this->createMock(ProviderHandlerInterface::class);
        $providerHandler->expects($this->never())->method('getItem');
        $providerHandler->expects($this->never())->method('saveItem');

        $this->execute($providerHandler, ['identifier' => 4, 'property' => 'id', 'value' => 1], ['id' => 'inline']);
    }

    public function testMissingItemIsNotSaved(): void
    {
        $this->execute(
            $this->getProviderHandler(new DataObject(), false),
            ['identifier' => 999, 'property' => 'name', 'value' => 'x']
        );
    }

    public function testHiddenActionsBlockInlineEdit(): void
    {
        $providerHandler = $this->createMock(ProviderHandlerInterface::class);
        $providerHandler->expects($this->never())->method('saveItem');

        $this->execute($providerHandler, ['identifier' => 4, 'property' => 'name', 'value' => 'x'], [], false);
    }

    public function testInvalidValueTypesAreIgnored(): void
    {
        $providerHandler = $this->createMock(ProviderHandlerInterface::class);
        $providerHandler->expects($this->never())->method('getItem');

        $this->execute($providerHandler, ['identifier' => [4], 'property' => 'name', 'value' => 'x']);
        $this->execute($providerHandler, ['identifier' => 4, 'property' => ['name'], 'value' => 'x']);
        $this->execute($providerHandler, ['identifier' => 4, 'property' => 'name', 'value' => ['x']]);
        $this->execute($providerHandler, ['identifier' => 4, 'property' => 'name']);
    }

    private function getProviderHandler(DataObject $item, bool $expectSave): ProviderHandlerInterface
    {
        $providerHandler = $this->createMock(ProviderHandlerInterface::class);
        $providerHandler->method('getItem')->willReturn($item);
        $providerHandler->expects($expectSave ? $this->once() : $this->never())->method('saveItem');

        return $providerHandler;
    }

    private function execute(
        ProviderHandlerInterface $providerHandler,
        array $inlineEdit,
        array $templates = ['name' => 'inline', 'entity_model' => 'default'],
        bool $allowActions = true
    ): void {
        $columns = [];
        foreach ($templates as $code => $template) {
            $columns[$code] = new Column(['code' => $code, 'cell_template' => $template]);
        }

        $viewModel = $this->createMock(GridViewModel::class);
        $viewModel->method('allowActions')->willReturn($allowActions);
        $viewModel->method('getColumns')->willReturn($columns);
        $viewModel->method('getCellTemplate')->willReturnCallback(
            fn (DataObject $item, Column $column) => $column->getCellTemplate() === 'inline'
                ? InlineEditAction::INLINE_EDIT_TEMPLATE
                : 'Loki_AdminComponents::grid/cell/default.phtml'
        );

        $component = $this->createMock(Component::class);
        $component->method('getViewModel')->willReturn($viewModel);

        $repository = $this->createMock(GridRepository::class);
        $repository->method('getComponent')->willReturn($component);
        $repository->method('getPrimaryKey')->willReturn('id');
        $repository->method('getProviderHandler')->willReturn($providerHandler);
        $repository->method('getProvider')->willReturn(new \stdClass());

        (new InlineEditAction())->execute($repository, ['inline_edit' => $inlineEdit]);
    }
}
