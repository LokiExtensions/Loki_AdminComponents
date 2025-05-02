<?php declare(strict_types=1);

namespace Yireo\LokiAdminComponents\Component\Form\FormRepository;

use Magento\Framework\DataObject;
use Magento\Framework\Message\Manager;
use Yireo\LokiAdminComponents\Component\Form\FormRepository;
use Yireo\LokiComponents\Util\CamelCaseConvertor;

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
        $dataProvider = $formRepository->getDataProvider();

        $item = $formRepository->getItemFromData($value);

        foreach ($value['item'] as $propertyKey => $propertyValue) {
            if ($propertyKey === $primaryKey) {
                continue;
            }

            $this->setItemPropertyValue($item, $propertyKey, $propertyValue);
        }

        $dataProvider->save($item);
        $this->messageManager->addSuccessMessage('Item saved');
    }

    private function setItemPropertyValue(DataObject $item, $propertyName, $propertyValue): void
    {
        $propertyMethod = 'set'.ucfirst($this->camelCaseConvertor->toCamelCase($propertyName));
        call_user_func([$item, $propertyMethod], $propertyValue);
    }
}
