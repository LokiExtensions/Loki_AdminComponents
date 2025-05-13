<?php declare(strict_types=1);

namespace Yireo\LokiAdminComponents\Component\Form;

use Magento\Framework\DataObject;
use Magento\Framework\UrlFactory;
use Yireo\LokiAdminComponents\Ui\Button;
use Yireo\LokiAdminComponents\Ui\ButtonFactory;
use Yireo\LokiComponents\Component\ComponentViewModel;

class FormViewModel extends ComponentViewModel
{
    public function __construct(
        protected UrlFactory $urlFactory,
        protected ButtonFactory $buttonFactory,
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
}
