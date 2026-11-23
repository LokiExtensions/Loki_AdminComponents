<?php declare(strict_types=1);

namespace Loki\AdminComponents\Test\Unit\Component\Grid;

use Loki\AdminComponents\Component\Grid\GridRepository;
use Loki\AdminComponents\Component\Grid\GridViewModel;
use Loki\AdminComponents\Form\Field\FieldFactory;
use Loki\AdminComponents\Grid\Cell\CellActionFactory;
use Loki\AdminComponents\Grid\Cell\CellTemplateResolver;
use Loki\AdminComponents\Grid\BookmarkLoader;
use Loki\AdminComponents\Grid\Column\Column;
use Loki\AdminComponents\Grid\Column\ColumnFactory;
use Loki\AdminComponents\Grid\ColumnLoader;
use Loki\AdminComponents\Grid\Filter\FilterFactory;
use Loki\AdminComponents\Grid\MassAction\MassActionFactory;
use Loki\AdminComponents\Grid\StateManager;
use Loki\AdminComponents\ProviderHandler\ProviderHandlerInterface;
use Loki\AdminComponents\Ui\Button\ButtonFactory;
use Loki\AdminComponents\Ui\Button\ButtonsResolver;
use Loki\Components\Component\ComponentInterface;
use Loki\Components\Util\CamelCaseConvertor;
use Magento\Framework\UrlFactory;
use Magento\Framework\View\Element\Template;
use PHPUnit\Framework\TestCase;

class GridViewModelColumnsTest extends TestCase
{
    public function testProviderLabelAndCellTemplateSurviveBookmarkMerge(): void
    {
        $columns = $this->getViewModel(
            [
                new Column([
                    'code' => 'name',
                    'label' => 'Provider label',
                    'cell_template' => 'Vendor_Module::name.phtml',
                    'visible' => true,
                ]),
            ],
            [
                new Column(['code' => 'name', 'visible' => false, 'position' => 4]),
            ]
        )->getColumns();

        $this->assertCount(1, $columns);
        $this->assertSame('Provider label', $columns[0]->getLabel());
        $this->assertSame('Vendor_Module::name.phtml', $columns[0]->getCellTemplate());
    }

    public function testBookmarkOnlyColumnsAreDropped(): void
    {
        $columns = $this->getViewModel(
            [new Column(['code' => 'name'])],
            [new Column(['code' => 'name']), new Column(['code' => 'obsolete'])]
        )->getColumns();

        $this->assertSame(['name'], array_map(fn (Column $column) => $column->getCode(), $columns));
    }

    public function testBookmarkVisibilityAndPositionAreApplied(): void
    {
        $columns = $this->getViewModel(
            [
                new Column(['code' => 'name', 'visible' => true, 'position' => 1]),
                new Column(['code' => 'email', 'visible' => true, 'position' => 2]),
                new Column(['code' => 'status', 'visible' => true, 'position' => 3]),
            ],
            [
                new Column(['code' => 'name', 'visible' => false, 'position' => 7]),
                new Column(['code' => 'email', 'position' => 0]),
            ]
        )->getColumns();

        $this->assertSame(['email', 'status', 'name'], array_map(fn (Column $column) => $column->getCode(), $columns));
        [$email, $status, $name] = $columns;

        $this->assertTrue($email->isVisible());
        $this->assertSame(0, $email->getPosition());
        $this->assertTrue($status->isVisible());
        $this->assertSame(3, $status->getPosition());
        $this->assertFalse($name->isVisible());
        $this->assertSame(7, $name->getPosition());
    }

    public function testColumnsAreLoadedOnlyOnce(): void
    {
        $viewModel = $this->getViewModel(
            [new Column(['code' => 'name'])],
            [new Column(['code' => 'name', 'position' => 1])],
            true
        );

        $columns = $viewModel->getColumns();
        $this->assertSame($columns, $viewModel->getColumns());
        $this->assertSame($viewModel->getAvailableColumns(), $viewModel->getAvailableColumns());
        $viewModel->getColumns();
    }

    private function getViewModel(
        array $providerColumns,
        array $bookmarkColumns,
        bool $expectSingleLoad = false
    ): GridViewModel {
        $provider = new \stdClass();

        $providerHandler = $this->createMock(ProviderHandlerInterface::class);
        $providerHandler->expects($expectSingleLoad ? $this->once() : $this->any())
            ->method('getColumns')
            ->with($provider)
            ->willReturn($providerColumns);

        $repository = $this->createStub(GridRepository::class);
        $repository->method('getProviderHandler')->willReturn($providerHandler);
        $repository->method('getProvider')->willReturn($provider);

        $columnLoader = $this->createMock(ColumnLoader::class);
        $columnLoader->expects($expectSingleLoad ? $this->once() : $this->any())
            ->method('getColumnsFromNamespace')
            ->with('ns')
            ->willReturn($bookmarkColumns);
        $columnLoader->method('getColumnsFromBlock')->willReturn([]);
        $columnLoader->method('sortColumns')->willReturnCallback(
            fn (array $columns) => (new ColumnLoader(
                $this->createStub(ColumnFactory::class),
                $this->createStub(BookmarkLoader::class),
                $this->createStub(StateManager::class)
            ))->sortColumns($columns)
        );

        $viewModel = new GridViewModel(
            $this->createStub(StateManager::class),
            $this->createStub(UrlFactory::class),
            $this->createStub(CellActionFactory::class),
            $this->createStub(CamelCaseConvertor::class),
            $this->createStub(CellTemplateResolver::class),
            $columnLoader,
            $this->createStub(ButtonFactory::class),
            $this->createStub(FieldFactory::class),
            $this->createStub(FilterFactory::class),
            $this->createStub(MassActionFactory::class),
            $this->createStub(ButtonsResolver::class),
        );

        $block = $this->createPartialMock(Template::class, []);
        $block->setData('namespace', 'ns');
        $viewModel->setBlock($block);

        $component = $this->createStub(ComponentInterface::class);
        $component->method('getRepository')->willReturn($repository);
        $viewModel->setComponent($component);

        return $viewModel;
    }
}
