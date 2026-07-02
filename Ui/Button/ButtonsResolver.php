<?php
declare(strict_types=1);

namespace Loki\AdminComponents\Ui\Button;

use Loki\AdminComponents\Provider\FormProviderInterface;
use Magento\Framework\DataObject;
use Magento\Framework\View\Element\AbstractBlock;

class ButtonsResolver
{
    public function __construct(
        private ButtonFactory $buttonFactory,
    ) {
    }

    /**
     * @return Button[]
     */
    public function resolve(
        AbstractBlock $block,
        object $provider,
        mixed $item
    ): array {
        if ($provider instanceof FormProviderInterface) {
            return $provider->getForm()->getButtons();
        }

        $buttonDefinitions = $block->getButtons();
        if (!empty($buttonDefinitions)) {
            $buttons = [];
            foreach ($buttonDefinitions as $buttonDefinition) {
                $buttons[] = $this->buttonFactory->create(
                    method: (string)$buttonDefinition['method'],
                    label: (string)$buttonDefinition['label'],
                    cssClass: isset($buttonDefinition['cssClass']) ? (string)$buttonDefinition['cssClass'] : '',
                    url: isset($buttonDefinition['url']) ? (string)$buttonDefinition['url'] : '',
                    primary: $buttonDefinition['primary'] ?? false,
                );
            }

            return $buttons;
        }

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
            // @todo: This looses current changes when creating a new item
            //$this->buttonFactory->createSaveContinueAction(),
        ];
    }
}
