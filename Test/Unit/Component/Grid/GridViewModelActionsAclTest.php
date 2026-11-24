<?php declare(strict_types=1);

namespace Loki\AdminComponents\Test\Unit\Component\Grid;

use Loki\AdminComponents\Component\Grid\GridViewModel;
use Loki\AdminComponents\Form\Field\FieldFactory;
use Loki\AdminComponents\Grid\Cell\CellAction;
use Loki\AdminComponents\Grid\Cell\CellActionFactory;
use Loki\AdminComponents\Grid\Cell\CellTemplateResolver;
use Loki\AdminComponents\Grid\ColumnLoader;
use Loki\AdminComponents\Grid\Filter\FilterFactory;
use Loki\AdminComponents\Grid\MassAction\MassActionFactory;
use Loki\AdminComponents\Grid\MassAction\MassActionInterface;
use Loki\AdminComponents\Grid\StateManager;
use Loki\AdminComponents\Ui\Button\ButtonFactory;
use Loki\AdminComponents\Ui\Button\ButtonsResolver;
use Loki\Components\Util\CamelCaseConvertor;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\DataObject;
use Magento\Framework\UrlFactory;
use Magento\Framework\View\Element\Template;
use PHPUnit\Framework\TestCase;

class GridViewModelActionsAclTest extends TestCase
{
    private const ACTIONS = [
        'open' => ['label' => 'Open', 'url' => 'vendor/entity/open'],
        'delete' => ['label' => 'Delete', 'url' => 'vendor/entity/delete', 'acl_resource' => 'Vendor_Module::delete'],
    ];

    public function testRowActionsAreFilteredByAclResource(): void
    {
        $this->assertSame(['Open'], $this->getRowActionLabels(false));
        $this->assertSame(['Open', 'Delete'], $this->getRowActionLabels(true));
    }

    public function testMassActionsAreFilteredByAclResource(): void
    {
        $this->assertSame(['open'], array_keys($this->getViewModel(false)->getMassActions()));
        $this->assertSame(['open', 'delete'], array_keys($this->getViewModel(true)->getMassActions()));
    }

    private function getRowActionLabels(bool $isAllowed): array
    {
        $actions = $this->getViewModel($isAllowed)->getCellActions(new DataObject(['id' => 1]));

        return array_map(fn (CellAction $action) => $action->getLabel(), $actions);
    }

    private function getViewModel(bool $isAllowed): GridViewModel
    {
        $authorization = $this->createMock(AuthorizationInterface::class);
        $authorization->method('isAllowed')->with('Vendor_Module::delete')->willReturn($isAllowed);

        $cellActionFactory = $this->createStub(CellActionFactory::class);
        $cellActionFactory->method('createFromData')->willReturnCallback(
            fn (array $data) => new CellAction($data['label'], $data['url'])
        );

        $massActionFactory = $this->createStub(MassActionFactory::class);
        $massActionFactory->method('create')->willReturnCallback(
            fn () => $this->createStub(MassActionInterface::class)
        );

        $viewModel = new GridViewModel(
            $this->createStub(StateManager::class),
            $this->createStub(UrlFactory::class),
            $cellActionFactory,
            $this->createStub(CamelCaseConvertor::class),
            $this->createStub(CellTemplateResolver::class),
            $this->createStub(ColumnLoader::class),
            $this->createStub(ButtonFactory::class),
            $this->createStub(FieldFactory::class),
            $this->createStub(FilterFactory::class),
            $massActionFactory,
            $this->createStub(ButtonsResolver::class),
            $authorization,
        );

        $block = $this->createPartialMock(Template::class, []);
        $block->setData('skip_edit_action', true);
        $block->setData('row_actions', self::ACTIONS);
        $block->setData('mass_actions', self::ACTIONS);
        $viewModel->setBlock($block);

        return $viewModel;
    }
}
