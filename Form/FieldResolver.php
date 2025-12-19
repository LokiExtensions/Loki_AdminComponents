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
        if (!$resourceModel) {
            return [];
        }

        $fields = [];
        $tableColumns = $resourceModel->getConnection()->describeTable($resourceModel->getMainTable());

        foreach ($tableColumns as $tableColumn) {
            $columnName = $tableColumn['COLUMN_NAME'];
            $fieldType = $this->getFieldTypeCodeFromColumn($formRepository, $tableColumn);
            if ($columnName === $resourceModel->getIdFieldName()) {
                $fieldType = 'view';
            }

            $fieldLabel = $this->getLabelByColumn($tableColumn['COLUMN_NAME']);
            $code = $tableColumn['COLUMN_NAME'];

            if (empty($fieldType)) {
                $fieldType = 'input';
            }

            $block = $this->layout->createBlock(Template::class);
            $fieldData = [
                'field_type' => $fieldType,
                'code' => $code,
                'label' => $fieldLabel,
                'required' => false,
                'sort_order' => 0,
                'field_attributes' => [],
                'label_attributes' => [],
            ];

            if (array_key_exists($columnName, $fieldDefinitions)) {
                $fieldDefinition = (array)$fieldDefinitions[$columnName];
                $fieldData = array_merge($fieldData, $fieldDefinition);
            }

            $fields[$code] = $this->fieldFactory->create(
                $block,
                $fieldData,
            );
        }

        uasort($fields, function (Field $field1, Field $field2) {
            return $field1->getSortOrder() <=> $field2->getSortOrder();
        });

        return $fields;
    }

    private function getFieldTypeCodeFromColumn(FormRepository $formRepository, array $tableColumn): false|string
    {
        if ($tableColumn['COLUMN_NAME'] === $formRepository->getPrimaryKey()) {
            return 'view';
        }

        if (in_array($tableColumn['DATA_TYPE'], ['datetime'])) {
            return 'datetime';
        }

        if (in_array($tableColumn['DATA_TYPE'], ['date'])) {
            return 'date';
        }

        if (in_array($tableColumn['DATA_TYPE'], ['tinyint'])) {
            return 'switch';
        }

        if (in_array($tableColumn['DATA_TYPE'], ['int'])) {
            return 'number';
        }

        if (in_array($tableColumn['DATA_TYPE'], ['varchar', 'text', 'smalltext', 'mediumtext'])) {
            return 'input';
        }

        return false;
    }

    private function getLabelByColumn(string $columnName): string
    {
        $label = (string)__($columnName);
        if ($label !== $columnName) {
            return $label;
        }

        return ucfirst(str_replace('_', ' ', $label));
    }
}
