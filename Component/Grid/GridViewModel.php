<?php declare(strict_types=1);

namespace Yireo\LokiAdminComponents\Component\Grid;

use Magento\Framework\DataObject;
use Magento\Framework\UrlFactory;
use Yireo\LokiAdminComponents\Grid\Cell\CellAction;
use Yireo\LokiAdminComponents\Grid\Cell\CellActionFactory;
use Yireo\LokiAdminComponents\Grid\State;
use Yireo\LokiComponents\Component\ComponentViewModel;
use Yireo\LokiComponents\Util\CamelCaseConvertor;

/**
 * @method GridRepository getRepository()
 */
class GridViewModel extends ComponentViewModel
{
    public function __construct(
        private State $state,
        protected UrlFactory $urlFactory,
        protected CellActionFactory $cellActionFactory,
        protected CamelCaseConvertor $camelCaseConvertor
    ) {
    }

    public function getSearchableFields(): array
    {
        return $this->getBlock()->getSearchableFields();
    }

    /**
     * @return DataObject[]
     */
    public function getItems(): array
    {
        return $this->getRepository()->getItems();
    }

    public function getValueFromItem(DataObject $item, mixed $propertyName): mixed
    {
        $propertyMethod = 'get'.ucfirst($this->camelCaseConvertor->toCamelCase($propertyName));
        return call_user_func([$item, $propertyMethod]);
    }

    public function getPage(): int
    {
        return $this->state->getPage();
    }

    public function getTotalPages(): int
    {
        return $this->state->getTotalPages();
    }

    public function getJsComponentName(): ?string
    {
        return 'LokiAdminGridComponent';
    }

    public function getJsData(): array
    {
        return [
            ...parent::getJsData(),
            ...$this->state->toArray(),
            'newUrl' => $this->getNewUrl(),
            'columns' => array_keys($this->getColumns()),
            'indexUrl' => $this->getIndexUrl(),
        ];
    }

    public function getColumns(): array
    {
        return (array)$this->getBlock()->getColumns();
    }

    public function getIndexUrl(): string
    {
        return $this->urlFactory->create()->getUrl('*/*/index');
    }

    public function getNewUrl(): string
    {
        return $this->urlFactory->create()->getUrl('*/*/create');
    }

    public function getCellTemplates(): array
    {
        $cellTemplates = (array)$this->getBlock()->getCellTemplates();

        if (!empty($cellTemplates)) {
            return $cellTemplates;
        }

        return [
            'thumbnail' => 'Yireo_LokiAdminComponents::grid/cell/product_image.phtml',
            'price' => 'Yireo_LokiAdminComponents::grid/cell/price.phtml',
            'visibility' => 'Yireo_LokiAdminComponents::grid/cell/visibility.phtml',
            'status' => 'Yireo_LokiAdminComponents::grid/cell/status.phtml',
            'type_id' => 'Yireo_LokiAdminComponents::grid/cell/product_type.phtml',
            'attribute_set_id' => 'Yireo_LokiAdminComponents::grid/cell/attribute_set_id.phtml',
        ];
    }

    /**
     * @param DataObject $item
     * @return CellAction[]
     */
    protected function getAdditionalActions(DataObject $item): array
    {
        return [];
    }

    public function getRowAction(DataObject $item): CellAction
    {
        $editUrl = $this->urlFactory->create()->getUrl('*/*/edit', [
            'id' => $item->getId(),
        ]);

        return $this->cellActionFactory->create($editUrl, 'Edit');
    }

    public function getCellActions(DataObject $item): array
    {
        $cellActions = [];
        $cellActions[] = $this->getRowAction($item);
        $cellActions = array_merge($cellActions, $this->getAdditionalActions($item));

        return $cellActions;
    }
}
