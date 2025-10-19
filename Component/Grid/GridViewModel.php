<?php declare(strict_types=1);

namespace Loki\AdminComponents\Component\Grid;

use Loki\AdminComponents\Grid\Column\Column;
use Loki\AdminComponents\Grid\Filter\FilterFactory;
use Loki\AdminComponents\Grid\Filter\StaticFilterInterface;
use Loki\AdminComponents\Grid\State\FilterState;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\UrlFactory;
use Loki\AdminComponents\Form\Field\Field;
use Loki\AdminComponents\Form\Field\FieldFactory;
use Loki\AdminComponents\Grid\Cell\CellAction;
use Loki\AdminComponents\Grid\Cell\CellActionFactory;
use Loki\AdminComponents\Grid\Cell\CellTemplateResolver;
use Loki\AdminComponents\Grid\ColumnLoader;
use Loki\AdminComponents\Grid\Filter\FilterInterface;
use Loki\AdminComponents\Grid\MassAction\MassActionInterface;
use Loki\AdminComponents\Grid\State;
use Loki\AdminComponents\Grid\StateManager;
use Loki\AdminComponents\Ui\Button;
use Loki\AdminComponents\Ui\ButtonFactory;
use Loki\Components\Component\ComponentViewModel;
use Loki\Components\Util\CamelCaseConvertor;
use RuntimeException;

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
        protected FieldFactory $fieldFactory,
        protected FilterFactory $filterFactory,
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
            if ($staticFilter instanceof StaticFilterInterface) {
                $filters[] = [
                    'field' => $staticFilter->getCode(),
                    'value' => $staticFilter->getValue(),
                    'condition_type' => $staticFilter->getConditionType(),
                ];
                continue;
            }

            $filters[] = [
                'field' => $staticFilter['field'],
                'value' => $staticFilter['value'],
                'condition_type' => $staticFilter['condition_type'],
            ];
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
            'namespace' => $this->getNamespace(),
            'gridFilters' => $this->getGridFilterStateValues(),
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

    /**
     * @return Column[]
     */
    private function getAvailableColumns(): array
    {
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
            $columns[$columnName] = $this->columnLoader->createColumn($columnName);
        }

        return $columns;
    }

    /**
     * @return Column[]
     */
    public function getColumns(): array
    {
        $columns = $this->getAvailableColumns();

        $columnsFromBlock = $this->columnLoader->getColumnsFromBlock($this->block);
        if (!empty($columnsFromBlock)) {
            foreach ($columnsFromBlock as $columnFromBlock) {
                foreach ($columns as $column) {
                    if ($column->getCode() !== $columnFromBlock->getCode()) {
                        continue;
                    }

                    $column->setData(array_merge($column->getData(), $columnFromBlock->getData()));
                }
            }
        }

        return $this->columnLoader->sortColumns($columns);
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

    public function getCellTemplate(DataObject $dataObject, Column $column): string
    {
        if ($column->getCellTemplate()) {
            return $column->getCellTemplate();
        }

        $propertyName = $column->getCode();
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
        $buttons = [];
        $buttonActions = (array)$this->getBlock()->getButtonActions();
        if (!empty($buttonActions)) {
            foreach ($buttonActions as $buttonAction) {
                if (!is_array($buttonAction)) {
                    continue;
                }

                if (!isset($buttonAction['method']) || !isset($buttonAction['label'])) {
                    continue;
                }

                $buttons[] = $this->buttonFactory->create(
                    (string)$buttonAction['method'],
                    (string)$buttonAction['label'],
                    isset($buttonAction['cssClass']) ? (string)$buttonAction['cssClass'] : '',
                    isset($buttonAction['url']) ? (string)$buttonAction['url'] : '',
                    isset($buttonAction['subButtons']) ? (string)$buttonAction['subButtons'] : [],
                    isset($buttonAction['primary']) ? (bool)$buttonAction['primary'] : false,
                );
            }
        }

        return $buttons;
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
     * @return FilterInterface[]
     */
    public function getGridFilters(): array
    {
        $gridFilters = [];
        $gridFilterDefinitions = (array)$this->getBlock()->getGridFilters();
        if (empty($gridFilterDefinitions)) {
            return [];
        }

        foreach ($gridFilterDefinitions as $gridFilterDefinition) {
            $gridFilters[] = $this->filterFactory->createFromArray($gridFilterDefinition);
        }

        return $gridFilters;
    }

    /**
     * @return FilterState[]
     */
    public function getGridFilterStates(): array
    {
        $gridFilterStates = [];
        foreach ($this->getGridFilters() as $gridFilter) {
            $filterState = $this->getState()->getFilterState($gridFilter->getCode());
            if (empty($filterState)) {
                continue;
            }

            $gridFilterStates[$gridFilter->getCode()] = $filterState;
        }

        return $gridFilterStates;
    }

    private function getGridFilterStateValues(): array
    {
        $values = [];
        foreach ($this->getGridFilterStates() as $gridFilterState) {
            $values[$gridFilterState->getField()] = $gridFilterState->getValue();
        }

        return $values;
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
        if ((bool)$this->getBlock()->getData('hide_actions') === true) {
            return false;
        }

        return true;
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
