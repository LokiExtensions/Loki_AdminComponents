<?php declare(strict_types=1);

namespace Yireo\LokiAdminComponents\Component\Grid;

use Magento\Framework\DataObject;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\UrlFactory;
use Yireo\LokiAdminComponents\Form\Field\Field;
use Yireo\LokiAdminComponents\Form\Field\FieldFactory;
use Yireo\LokiAdminComponents\Grid\Cell\CellAction;
use Yireo\LokiAdminComponents\Grid\Cell\CellActionFactory;
use Yireo\LokiAdminComponents\Grid\Cell\CellTemplateResolver;
use Yireo\LokiAdminComponents\Grid\ColumnLoader;
use Yireo\LokiAdminComponents\Grid\FilterInterface;
use Yireo\LokiAdminComponents\Grid\MassAction\MassActionInterface;
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
    private array $items = [];

    public function __construct(
        protected StateManager $stateManager,
        protected UrlFactory $urlFactory,
        protected CellActionFactory $cellActionFactory,
        protected CamelCaseConvertor $camelCaseConvertor,
        protected CellTemplateResolver $cellTemplateResolver,
        protected ColumnLoader $columnLoader,
        protected ObjectManagerInterface $objectManager,
        protected ButtonFactory $buttonFactory,
        protected FieldFactory $fieldFactory
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
        if (!empty($this->items[$this->getNamespace()])) {
            return $this->items[$this->getNamespace()];
        }

        $this->applyStaticFilters();
        $this->items[$this->getNamespace()] = $this->getRepository()->getItems();

        return $this->items[$this->getNamespace()];
    }

    public function applyStaticFilters(): void
    {
        $staticFilters = (array)$this->getBlock()->getStaticFilters();
        if (empty($staticFilters)) {
            return;
        }

        $filters = [];
        foreach ($staticFilters as $staticFilter) {
            if ($staticFilter instanceof FilterInterface) {
                $filters[] = [
                    'field' => $staticFilter->getField(),
                    'value' => $staticFilter->getValue(),
                    'condition_type' => $staticFilter->getConditionType(),
                ];
                continue;
            }

            $filter = [];
            $filter['field'] = $staticFilter['field'];
            $filter['value'] = $staticFilter['value'];
            $filter['condition_type'] = $staticFilter['condition_type'];
            $filters[] = $filter;
        }

        $this->getState()->setFilters($filters);
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
            'columnPositions' => $this->getColumnPositions(),
            'newUrl' => $this->getNewUrl(),
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

        return $this->cellTemplateResolver->resolve($dataObject, $propertyName, $this->getNamespace());
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

    /**
     * @return MassActionInterface[]
     */
    public function getMassActions(): array
    {
        return (array)$this->getBlock()->getMassActions();
    }

    public function getRowAction(DataObject $item): CellAction
    {
        // @todo: Retrieve ID from primary key
        $editUrl = $this->getEditUrl(['id' => $item->getId()]);

        return $this->cellActionFactory->create($editUrl, 'Edit');
    }


    /**
     * @return Field[]
     */
    public function getFilterFields(): array
    {
        $filters = (array)$this->getBlock()->getData('grid_filters');
        if (empty($filters)) {
            return [];
        }

        $fields = [];
        foreach ($filters as $filterCode => $filter) {
            $field = $this->fieldFactory->createWithBlock(
                $filter['field_type'],
                $filter['label'],
                $filterCode,
            );

            if (isset($filter['options']) && is_string($filter['options'])) {
                $filter['options'] = $this->objectManager->get($filter['options']);
            }

            $field->getBlock()->addData($filter);
            $field->getBlock()->setField($field);
            $fields[] = $field;
        }

        return $fields;
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

    public function getState(): State
    {
        return $this->stateManager->get($this->getNamespace());
    }

    /**
     * @param DataObject $item
     * @return CellAction[]
     */
    protected function getAdditionalActions(DataObject $item): array
    {
        $actions = [];
        $actionsFromBlock = (array)$this->getBlock()->getRowActions();
        if (!empty($actionsFromBlock)) {
            foreach ($actionsFromBlock as $action) {
                $params = ['id' => $item->getId()];
                $url = $this->urlFactory->create()->getUrl($action['url'], $params);
                $actions[] = $this->cellActionFactory->create($url, $action['label']);
            }
        }

        return $actions;
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
        return $this->getRepository()->getResourceModel();
    }
}
