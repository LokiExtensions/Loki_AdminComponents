<?php declare(strict_types=1);

namespace Loki\AdminComponents\Component\Form;

use Loki\AdminComponents\Form\Field\Field;
use Loki\AdminComponents\Form\Field\FieldFactory;
use Loki\AdminComponents\Form\FieldResolver;
use Loki\AdminComponents\Form\Fieldset\Fieldset;
use Loki\AdminComponents\Form\Fieldset\FieldsetFactory;
use Loki\AdminComponents\Form\ItemConvertorInterface;
use Loki\AdminComponents\Ui\Button;
use Loki\AdminComponents\Ui\ButtonFactory;
use Loki\Components\Component\ComponentViewModel;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\UrlFactory;

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
        protected FieldsetFactory $fieldsetFactory,
        protected RequestInterface $request,
        protected FieldResolver $fieldResolver,
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
     * @return Fieldset[]
     */
    public function getFieldsets(): array
    {
        $fieldsetDefinitions = (array)$this->getBlock()->getFieldsets();
        $fieldsets['base'] = $this->fieldsetFactory->create('base', $fieldsetDefinitions['base']['label'] ?? null);

        foreach ($fieldsetDefinitions as $fieldsetCode => $fieldsetDefinition) {
            if (empty($fieldsetCode)) {
                $fieldsetCode = 'base';
            }

            if (array_key_exists($fieldsetCode, $fieldsets)) {
                continue;
            }

            $fieldsets[$fieldsetCode] = $this->fieldsetFactory->create($fieldsetCode, $fieldsetDefinition['label']);
        }

        $fieldDefinitions = (array)$this->getBlock()->getFields();
        $fields = $this->fieldResolver->resolve($this->getRepository(), $fieldDefinitions);

        foreach ($fields as $field) {
            $fieldsetCode = (string)$field->getFieldset();

            if (isset($fieldDefinitions[$field->getCode()])) {
                unset($fieldDefinitions[$field->getCode()]);
            }

            if (!empty($fieldsetCode) && array_key_exists($fieldsetCode, $fieldsets)) {
                $fieldsets[$fieldsetCode]->addField($field);
                continue;
            }

            $fieldsets['base']->addField($field);
        }

        foreach ($fieldDefinitions as $fieldDefinitionName => $fieldDefinition) {
            if (!isset($fieldDefinition['code'])) {
                $fieldDefinition['code'] = $fieldDefinitionName;
            }

            $field = $this->fieldFactory->create($this->getBlock(), $fieldDefinition);

            if (!empty($fieldsetCode) && array_key_exists($fieldsetCode, $fieldsets)) {
                $fieldsets[$fieldsetCode]->addField($field);
                continue;
            }

            $fieldsets['base']->addField($field);
        }

        return $fieldsets;
    }

    /**
     * @return Field[]
     * @throws LocalizedException
     * @deprecated Use getFieldsets() instead
     */
    public function getFields(): array
    {
        $fields = $this->fieldResolver->resolve(
            $this->getRepository(),
            (array)$this->getBlock()->getFields(),
        );

        return $fields;
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

            return $currentUriParts[0].'/'.$currentUriParts[1].'/index';
        }

        return '*/*/index';
    }
}
