<?php declare(strict_types=1);

namespace Loki\AdminComponents\Form\Action;

use Loki\AdminComponents\Form\ItemConvertorInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Message\Manager;
use Loki\AdminComponents\Component\Form\FormRepository;
use Loki\Components\Util\CamelCaseConvertor;

class SaveAction implements ActionInterface
{
    public function __construct(
        private Manager $messageManager,
        private CamelCaseConvertor $camelCaseConvertor,
    ) {
    }

    public function execute(FormRepository $formRepository, array $value): void
    {
        $primaryKey = $formRepository->getPrimaryKey();
        $provider = $formRepository->getProvider();
        $providerHandler = $formRepository->getProviderHandler();

        $item = $formRepository->getItemFromData($value);

        foreach ($value['item'] as $propertyKey => $propertyValue) {
            if ($propertyKey === $primaryKey) {
                continue;
            }

            if (is_array($propertyValue)) {
                // @todo: Fix this because it breaks a lot of logic
                continue;
            }

            $this->setItemPropertyValue($item, $propertyKey, $propertyValue);
        }

        $itemConvertor = $formRepository->getComponent()->getViewModel()->getBlock()->getItemConvertor();
        if ($itemConvertor instanceof ItemConvertorInterface) {
            $item = $itemConvertor->beforeSave($item);
        }

        $providerHandler->saveItem($provider, $item);
        $this->messageManager->addSuccessMessage('Item saved');
    }

    private function setItemPropertyValue(DataObject $item, $propertyName, $propertyValue): void
    {
        $propertyMethod = 'set'.ucfirst($this->camelCaseConvertor->toCamelCase($propertyName));
        call_user_func([$item, $propertyMethod], $propertyValue);
    }
}
