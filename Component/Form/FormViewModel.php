<?php declare(strict_types=1);

namespace Yireo\LokiAdminComponents\Component\Form;

use Magento\Framework\DataObject;
use Magento\Framework\UrlFactory;
use Yireo\LokiAdminComponents\Form\FormAction\FormAction;
use Yireo\LokiAdminComponents\Form\FormAction\FormActionFactory;
use Yireo\LokiComponents\Component\ComponentViewModel;

class FormViewModel extends ComponentViewModel
{
    public function __construct(
        protected UrlFactory $urlFactory,
        protected FormActionFactory $formActionFactory,
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
            'item' => $this->getValue()->toArray(),
            'indexUrl' => $this->getIndexUrl(),
        ];
    }

    public function getIndexUrl(): string
    {
        return $this->urlFactory->create()->getUrl('*/*/index');
    }

    /**
     * @return FormAction[]
     */
    public function getFormActions(): array
    {
        $item = $this->getValue();
        if ($item instanceof DataObject && $item->getId() > 0) {
            return [
                $this->formActionFactory->createCloseAction(),
                $this->formActionFactory->createDeleteAction(),
                $this->formActionFactory->createSaveContinueAction(),
                $this->formActionFactory->createSaveDuplicateAction(),
                $this->formActionFactory->createSaveCloseAction(),
            ];
        }

        return [
            $this->formActionFactory->createCloseAction(),
            $this->formActionFactory->createSaveCloseAction(),
            //$this->formActionFactory->createSaveContinueAction(), // @todo: This looses current changes when creating a new item
        ];
    }
}
