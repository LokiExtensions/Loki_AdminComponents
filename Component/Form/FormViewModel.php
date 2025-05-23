<?php declare(strict_types=1);

namespace Yireo\LokiAdminComponents\Component\Form;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\DataObject;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\UrlFactory;
use Yireo\LokiAdminComponents\Form\Field\Field;
use Yireo\LokiAdminComponents\Form\Field\FieldFactory;
use Yireo\LokiAdminComponents\Ui\Button;
use Yireo\LokiAdminComponents\Ui\ButtonFactory;
use Yireo\LokiComponents\Component\ComponentViewModel;

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
        protected RequestInterface $request
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
            'item' => $this->getRepository()->getItem()?->toArray(),
            'indexUrl' => $this->getIndexUrl(),
        ];
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

        $fields = [];
        $tableColumns = $resourceModel->getConnection()->describeTable($resourceModel->getMainTable());

        foreach ($tableColumns as $tableColumn) {
            $fieldTypeCode = $this->getFieldTypeCodeFromColumn($tableColumn);
            if (empty($fieldTypeCode)) {
                echo 'Unknown field type: '.$tableColumn['DATA_TYPE'];
                continue;
            }

            $fields[] = $this->fieldFactory->create(
                $this->getBlock(),
                $fieldTypeCode,
                $this->getLabelByColumn($tableColumn['COLUMN_NAME']),
                $tableColumn['COLUMN_NAME']
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
