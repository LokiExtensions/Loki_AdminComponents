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
use Yireo\LokiAdminComponents\Grid\StateManager;
use Yireo\LokiAdminComponents\Ui\Button;
use Yireo\LokiAdminComponents\Ui\ButtonFactory;
use Yireo\LokiComponents\Component\ComponentViewModel;
use Yireo\LokiComponents\Util\CamelCaseConvertor;

/**
 * @method GridRepository getRepository()
 */
class GridViewModel extends ComponentViewModel
{
    public function __construct(
        protected StateManager $stateManager,
        protected UrlFactory $urlFactory,
        protected CellActionFactory $cellActionFactory,
        protected CamelCaseConvertor $camelCaseConvertor,
        protected CellTemplateResolver $cellTemplateResolver,
        protected ColumnLoader $columnLoader,
        protected ObjectManagerInterface $objectManager,
        protected ButtonFactory $buttonFactory,
    ) {
    }

    public function getNamespace(): string
    {
        $namespace = (string)$this->getBlock()->getNamespace();
        if (!empty($namespace)) {
            return $namespace;
        }

        return $this->getBlock()->getNameInLayout();
    }

    public function getSearchableFields(): array
    {
        $searchableFields = (array)$this->getBlock()->getSearchableFields();
        if (!empty($searchableFields)) {
            return $searchableFields;
        }

        $searchableFields = $this->getSearchableFieldsFromResourceModel();
        if (!empty($searchableFields)) {
            return $searchableFields;
        }

        return $this->getRepository()->getProviderHandler()->getColumns($this->getRepository()->getProvider());
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
        return $this->getState()->getPage();
    }

    public function getTotalPages(): int
    {
        return $this->getState()->getTotalPages();
    }

    public function getJsComponentName(): ?string
    {
        return 'LokiAdminGridComponent';
    }

    public function getJsData(): array
    {
        return [
            ...parent::getJsData(),
            ...$this->getState()->toArray(),
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
        // @todo: Move all logic here to column loader
        $columns = (array)$this->getBlock()->getColumns();
        if (!empty($columns)) {
            return $columns;
        }

        $columns = $this->columnLoader->getColumns($this->getNamespace());
        if (!empty($columns)) {
            return $columns;
        }

        $columns = $this->getRepository()->getProviderHandler()->getColumns($this->getRepository()->getProvider());
        if (!empty($columns)) {
            return $columns;
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
        $indexUrl = $this->getBlock()->getData('index_url');
        if (empty($indexUrl)) {
            $indexUrl = '*/*/index';
        }

        return $this->urlFactory->create()->getUrl($indexUrl);
    }

    public function getNewUrl(): string
    {
        $newUrl = $this->getBlock()->getData('new_url');
        $newUrl = 'catalog/product/new';
        if (empty($newUrl)) {
            $newUrl = '*/*/form';
        }

        return $this->urlFactory->create()->getUrl($newUrl);
    }

    public function getEditUrl(array $params = []): string
    {
        $editUrl = $this->getBlock()->getData('edit_url');
        if (empty($editUrl)) {
            $editUrl = '*/*/form';
        }

        return $this->urlFactory->create()->getUrl($editUrl, $params);
    }

    public function getCellTemplate(DataObject $dataObject, string $propertyName): string
    {
        $cellTemplates = $this->getCellTemplates();
        if (!empty($cellTemplates) && array_key_exists($propertyName, $cellTemplates)) {
            return (string)$cellTemplates[$propertyName];
        }

        return $this->cellTemplateResolver->resolve($dataObject, $propertyName);
    }

    public function getCellTemplates(): array
    {
        return (array)$this->getBlock()->getCellTemplates();
    }

    /**
     * @return Button[]
     */
    public function getButtons(): array
    {
        return [
            $this->buttonFactory->createNewAction(),
        ];
    }

    public function getRowAction(DataObject $item): CellAction
    {
        $editUrl = $this->getEditUrl(['id' => $item->getId()]);
        return $this->cellActionFactory->create($editUrl, 'Edit');
    }

    public function getCellActions(DataObject $item): array
    {
        if (false === $this->allowActions()) {
            return [];
        }

        $cellActions = [];
        $cellActions[] = $this->getRowAction($item);
        $cellActions = array_merge($cellActions, $this->getAdditionalActions($item));

        return $cellActions;
    }

    public function allowActions(): bool
    {
        if ($this->getBlock()->hasData('allow_actions')) {
            return (bool)$this->getBlock()->getData('allow_actions');
        }

        return $this->getRepository()->getProviderHandler()->allowActions(
            $this->getRepository()->getProvider()
        );
    }

    protected function getState(): State
    {
        return $this->stateManager->get($this->getNamespace());
    }

    /**
     * @param DataObject $item
     * @return CellAction[]
     */
    protected function getAdditionalActions(DataObject $item): array
    {
        return [];
    }

    private function getSearchableFieldsFromResourceModel(): array
    {
        $resourceModel = $this->getResourceModel();
        if (!$resourceModel) {
            return [];
        }

        $searchableFields = [];
        $tableColumns = $resourceModel->getConnection()->describeTable($resourceModel->getMainTable());
        foreach ($tableColumns as $tableColumn) {
            if (in_array($tableColumn['DATA_TYPE'], ['varchar', 'text', 'smalltext', 'mediumtext'])) {
                $searchableFields[] = $tableColumn['COLUMN_NAME'];
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
}
