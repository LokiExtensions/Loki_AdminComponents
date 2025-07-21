<?php declare(strict_types=1);

namespace Loki\AdminComponents\Component\Form;

use Loki\AdminComponents\Form\ItemConvertorInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\DataObject;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\UrlFactory;
use Magento\Framework\View\Element\Template;
use Loki\AdminComponents\Form\Field\Field;
use Loki\AdminComponents\Form\Field\FieldFactory;
use Loki\AdminComponents\Ui\Button;
use Loki\AdminComponents\Ui\ButtonFactory;
use Loki\Components\Component\ComponentViewModel;

/**
 * @method FormRepository getRepository()
 */
class FormViewModel extends ComponentViewModel
{
    public function __construct(
        protected UrlFactory $urlFactory,
        protected ButtonFactory $buttonFactory,
        protected ObjectManagerInterface $objectManager,
        protected FieldFactory $fieldFactory,
        protected RequestInterface $request,
        protected array $itemFilters = [],
    ) {
    }

    public function getJsComponentName(): ?string
    {
        return 'LokiAdminFormComponent';
    }

    public function getJsData(): array
    {
        return [
            ...parent::getJsData(),
            'item' => $this->getItemData(),
            'indexUrl' => $this->getIndexUrl(),
        ];
    }

    private function getItemData(): array
    {
        $item = $this->getRepository()->getItem();
        if (false === $item instanceof DataObject) {
            return [];
        }

        $itemConvertor = $this->getBlock()->getItemConvertor();
        if ($itemConvertor instanceof ItemConvertorInterface) {
            $item = $itemConvertor->afterLoad($item);
        }

        return $item->toArray();
    }

    public function getIndexUrl(): string
    {
        return $this->urlFactory->create()->getUrl($this->getIndexUri());
    }

    /**
     * @return Button[]
     * @todo Move this to Form
     */
    public function getButtons(): array
    {
        $item = $this->getValue();
        if ($item instanceof DataObject && $item->getId() > 0) {
            return [
                $this->buttonFactory->createCloseAction(),
                $this->buttonFactory->createDeleteAction(),
                $this->buttonFactory->createSaveContinueAction(),
                $this->buttonFactory->createSaveDuplicateAction(),
                $this->buttonFactory->createSaveCloseAction(),
            ];
        }

        return [
            $this->buttonFactory->createCloseAction(),
            $this->buttonFactory->createSaveCloseAction(),
            //$this->buttonFactory->createSaveContinueAction(), // @todo: This looses current changes when creating a new item
        ];
    }

    /**
     * @return Field[]
     * @throws \Magento\Framework\Exception\LocalizedException
     * @todo Move this to Form
     */
    public function getFields(): array
    {
        $resourceModel = $this->getRepository()->getResourceModel();
        if (!$resourceModel) {
            return [];
        }

        $fieldDefinitions = (array)$this->getBlock()->getFields();

        $fields = [];
        $tableColumns = $resourceModel->getConnection()->describeTable($resourceModel->getMainTable());

        foreach ($tableColumns as $tableColumn) {
            $columnName = $tableColumn['COLUMN_NAME'];
            $fieldTypeCode = $this->getFieldTypeCodeFromColumn($tableColumn);
            if (empty($fieldTypeCode)) {
                // @todo: echo 'Unknown field type: '.$tableColumn['DATA_TYPE'];
                continue;
            }

            $htmlAttributes = [];
            $block = $this->getBlock()->getLayout()->createBlock(Template::class);

            if (array_key_exists($columnName, $fieldDefinitions)) {
                $fieldDefinition = (array)$fieldDefinitions[$columnName];
                if (isset($fieldDefinition['field_type'])) {
                    $fieldTypeCode = $fieldDefinition['field_type'];
                }

                if (isset($fieldDefinition['html_attributes'])) {
                    $htmlAttributes = array_merge($htmlAttributes, $fieldDefinition['html_attributes']);
                }

                $block->addData($fieldDefinition);
            }

            $fields[] = $this->fieldFactory->create(
                $block,
                $fieldTypeCode,
                $this->getLabelByColumn($tableColumn['COLUMN_NAME']),
                $tableColumn['COLUMN_NAME'],
                false, // @todo
                $htmlAttributes
            );
        }

        return $fields;
    }

    private function getFieldTypeCodeFromColumn(array $tableColumn): false|string
    {
        if ($tableColumn['COLUMN_NAME'] === $this->getRepository()->getPrimaryKey()) {
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
            return 'text';
        }

        return false;
    }

    private function getIndexUri(): string
    {
        $indexUrl = $this->getBlock()->getIndexUrl();
        if (!empty($indexUrl)) {
            return $indexUrl;
        }


        $currentUri = (string)$this->request->getParam('currentUri');
        if (!empty($currentUri)) {
            $currentUriParts = explode('_', $currentUri);
            return $currentUriParts[0] . '/' . $currentUriParts[1] . '/index';
        }

        return '*/*/index';
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
