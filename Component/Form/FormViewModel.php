<?php declare(strict_types=1);

namespace Yireo\LokiAdminComponents\Component\Form;

use Magento\Framework\DataObject;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\UrlFactory;
use Yireo\LokiAdminComponents\Form\Field\Field;
use Yireo\LokiAdminComponents\Form\Field\FieldFactory;
use Yireo\LokiAdminComponents\Ui\Button;
use Yireo\LokiAdminComponents\Ui\ButtonFactory;
use Yireo\LokiComponents\Component\ComponentViewModel;

class FormViewModel extends ComponentViewModel
{
    public function __construct(
        protected UrlFactory $urlFactory,
        protected ButtonFactory $buttonFactory,
        protected ObjectManagerInterface $objectManager,
        protected FieldFactory $fieldFactory
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
        return $this->urlFactory->create()->getUrl('*/*/index');
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
        $resourceModel = $this->getResourceModel();
        if (!$resourceModel) {
            return [];
        }

        $fields = [];
        $tableColumns = $resourceModel->getConnection()->describeTable($resourceModel->getMainTable());

        foreach ($tableColumns as $tableColumn) {
            if (in_array($tableColumn['DATA_TYPE'], ['varchar', 'text', 'smalltext', 'mediumtext'])) {
                $fieldTypeCode = 'text';

                $fields[] = $this->fieldFactory->create(
                    $this->getBlock(),
                    $fieldTypeCode,
                    $tableColumn['COLUMN_NAME'],
                    $tableColumn['COLUMN_NAME'],
                    false
                );
            }
        }

        return $fields;
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
