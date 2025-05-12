<?php declare(strict_types=1);

namespace Yireo\LokiAdminComponents\Component\Grid;

use Magento\Framework\DataObject;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\UrlFactory;
use Yireo\LokiAdminComponents\Grid\Cell\CellAction;
use Yireo\LokiAdminComponents\Grid\Cell\CellActionFactory;
use Yireo\LokiAdminComponents\Grid\Cell\CellTemplateResolver;
use Yireo\LokiAdminComponents\Grid\ColumnLoader;
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
        protected CamelCaseConvertor $camelCaseConvertor,
        protected CellTemplateResolver $cellTemplateResolver,
        protected ColumnLoader $columnLoader,
        protected ObjectManagerInterface $objectManager
    ) {
    }

    public function getSearchableFields(): array
    {
        $searchableFields = (array)$this->getBlock()->getSearchableFields();
        if (!empty($searchableFields)) {
            return $searchableFields;
        }

        return $this->getSearchableFieldsFromResourceModel();
    }

    private function getSearchableFieldsFromResourceModel(): array
    {
        $resourceModel = $this->getResourceModel();
        if (!$resourceModel) {
            return [];
        }

        $searchableFields = [];
        $fields = $resourceModel->getConnection()->describeTable($resourceModel->getMainTable());
        foreach ($fields as $field) {
            if (in_array($field['DATA_TYPE'], ['varchar', 'text', 'smalltext', 'mediumtext'])) {
                $searchableFields[] = $field['COLUMN_NAME'];
            }
        }

        return $searchableFields;
    }

    private function getResourceModel(): ?AbstractDb
    {
        $resourceModelClass = $this->getBlock()->getResourceModel();
        if (empty($resourceModelClass)) {
            return null;
        }

        $resourceModel = $this->objectManager->get($resourceModelClass);
        if (false === $resourceModel instanceof AbstractDb) {
            return null;
        }

        return $resourceModel;
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
            'columnPositions' => $this->getColumnPositions(),
            'indexUrl' => $this->getIndexUrl(),
        ];
    }

    public function getColumnPositions(): array
    {
        $columnPositions = [];
        $position = 0;
        foreach ($this->getColumns() as $columnName => $columnLabel) {
            $columnPositions[$columnName] = $position;
            $position++;
        }

        return $columnPositions;
    }

    public function getColumns(): array
    {
        $columns = (array)$this->getBlock()->getColumns();
        if (!empty($columns)) {
            return $columns;
        }

        $namespace = (string)$this->getBlock()->getNamespace();
        if (!empty($namespace)) {
            return $this->columnLoader->getColumns($namespace);
        }

        $items = $this->getItems();
        if (empty($items)) {
            return [];
        }

        $item = array_shift($items);
        $itemData = $item->getData();
        $columns = [];
        foreach (array_keys($itemData) as $columnName) {
            $columns[$columnName] = $this->columnLoader->getLabelByColumn($columnName);
        }

        return $columns;
    }

    public function getIndexUrl(): string
    {
        return $this->urlFactory->create()->getUrl('*/*/index');
    }

    public function getNewUrl(): string
    {
        return $this->urlFactory->create()->getUrl('*/*/form');
    }

    public function getCellTemplate(DataObject $dataObject, string $propertyName): string
    {
        $cellTemplates = (array)$this->getBlock()->getCellTemplates();

        if (!empty($cellTemplates) && array_key_exists($propertyName, $cellTemplates)) {
            return (string)$cellTemplates[$propertyName];
        }

        return $this->cellTemplateResolver->resolve($dataObject, $propertyName);
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
        $editUrl = $this->urlFactory->create()->getUrl('*/*/form', [
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
