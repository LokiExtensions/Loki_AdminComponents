<?php declare(strict_types=1);

namespace Loki\AdminComponents\Form;

use Loki\AdminComponents\Component\Form\FormRepository;
use Loki\AdminComponents\Form\Field\Field;
use Loki\AdminComponents\Form\Field\FieldFactory;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\LayoutInterface;

class FieldResolver
{
    public function __construct(
        private FieldFactory $fieldFactory,
        private LayoutInterface $layout,
    ) {
    }

    public function resolve(FormRepository $formRepository, array $fieldDefinitions): array
    {
        $resourceModel = $formRepository->getResourceModel();

        if ($resourceModel === null) {
            return [];
        }

        $tableColumns = $this->getTableColumns($resourceModel);
        $fieldColumns = $this->mergeTableAndExtraColumns(
            $tableColumns,
            $fieldDefinitions
        );

        $fields = $this->buildFields(
            $formRepository,
            $tableColumns,
            $fieldColumns,
            $fieldDefinitions
        );

        $this->sortFieldsBySortOrder($fields);

        return $fields;
    }

    private function getTableColumns(object $resourceModel): array
    {
        return $resourceModel
            ->getConnection()
            ->describeTable($resourceModel->getMainTable());
    }

    private function mergeTableAndExtraColumns(
        array $tableColumns,
        array $fieldDefinitions
    ): array {
        $tableColumnNames = array_keys($tableColumns);

        $extraFieldDefinitions = array_filter(
            $fieldDefinitions,
            static fn (array $definition, string $fieldCode): bool =>
            !in_array($fieldCode, $tableColumnNames, true),
            ARRAY_FILTER_USE_BOTH
        );

        return array_merge($tableColumns, $extraFieldDefinitions);
    }

    private function buildFields(
        FormRepository $formRepository,
        array $tableColumns,
        array $fieldColumns,
        array $fieldDefinitions
    ): array {
        $fields = [];

        foreach ($fieldColumns as $fieldCode => $definition) {
            $columnData = $this->buildBaseColumnData(
                $formRepository,
                $tableColumns,
                $fieldCode
            );

            if (array_key_exists($fieldCode, $fieldDefinitions)) {
                $columnData = array_merge(
                    $columnData,
                    $fieldDefinitions[$fieldCode]
                );
            }

            $fields[$fieldCode] = $this->createField($columnData);
        }

        return $fields;
    }

    private function buildBaseColumnData(
        FormRepository $formRepository,
        array $tableColumns,
        string $fieldCode
    ): array {
        $columnData = ['code' => $fieldCode];

        if (!array_key_exists($fieldCode, $tableColumns)) {
            return $columnData;
        }

        $tableColumn = $tableColumns[$fieldCode];
        $fieldType = $this->resolveFieldType(
            $formRepository,
            $tableColumn
        );

        return array_merge($columnData, [
            'field_type' => $fieldType ?: 'input',
            'label' => $this->getLabelByColumn($tableColumn['COLUMN_NAME']),
            'required' => false,
            'sort_order' => 0,
            'field_attributes' => [],
            'label_attributes' => [],
        ]);
    }

    private function createField(array $columnData): Field
    {
        return $this->fieldFactory->create(
            $this->layout->createBlock(Template::class),
            $columnData,
        );
    }

    private function sortFieldsBySortOrder(array &$fields): void
    {
        uasort(
            $fields,
            static fn (Field $a, Field $b): int =>
                $a->getSortOrder() <=> $b->getSortOrder()
        );
    }

    private function resolveFieldType(
        FormRepository $formRepository,
        array $tableColumn
    ): false|string {
        if ($tableColumn['COLUMN_NAME'] === $formRepository->getPrimaryKey()) {
            return 'view';
        }

        return match ($tableColumn['DATA_TYPE']) {
            'datetime' => 'datetime',
            'date' => 'date',
            'tinyint' => 'switch',
            'int' => 'number',
            'varchar', 'text', 'smalltext', 'mediumtext' => 'input',
            default => false,
        };
    }

    private function getLabelByColumn(string $columnName): string
    {
        $translated = (string) __($columnName);
        if ($translated !== $columnName) {
            return $translated;
        }

        return ucfirst(str_replace('_', ' ', $columnName));
    }
}
