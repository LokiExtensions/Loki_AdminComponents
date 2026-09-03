<?php
declare(strict_types=1);

namespace Loki\AdminComponents\Ui\Button\ButtonsResolver;

use Loki\AdminComponents\Component\Form\FormRepository;
use Loki\AdminComponents\Ui\Button\ButtonFactory;
use Loki\Components\Component\ComponentRepository;
use Magento\Framework\DataObject;

class DefaultFormButtonsResolver implements ButtonsResolverInterface
{
    public function __construct(
        private ButtonFactory $buttonFactory,
    ) {
    }

    public function resolve(ComponentRepository $repository, array $buttons = []): array
    {
        if (!empty($buttons)) {
            return $buttons;
        }

        if (false === $repository instanceof FormRepository) {
            return [];
        }

        $item = $repository->getItem();
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
