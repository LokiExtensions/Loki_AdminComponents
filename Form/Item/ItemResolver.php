<?php
declare(strict_types=1);

namespace Loki\AdminComponents\Form\Item;

use Loki\AdminComponents\Component\Form\FormRepository;
use Loki\AdminComponents\Exception\NoProviderException;
use Loki\AdminComponents\Provider\ItemProviderInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\View\Element\AbstractBlock;
use Throwable;

class ItemResolver
{
    public function __construct(
        private ManagerInterface $messageManager,
    ) {
    }

    public function resolve(
        FormRepository $formRepository,
        AbstractBlock $block,
    ): ?DataObject {
        try {
            $provider = $formRepository->getProvider();
            if ($provider instanceof ItemProviderInterface) {
                return $provider->getItem((int)$formRepository->getValue());
            }
        } catch (NoProviderException $e) {
        }

        try {
            $item = $formRepository->getItem();
        } catch (Throwable $e) {
            $this->messageManager->addErrorMessage($e->getMessage());

            return null;
        }

        if (false === $item instanceof DataObject) {
            return null;
        }

        $itemConvertor = $block->getItemConvertor();
        if ($itemConvertor instanceof ItemConvertorInterface) {
            $item = $itemConvertor->afterLoad($item);
        }

        return $item;
    }
}
