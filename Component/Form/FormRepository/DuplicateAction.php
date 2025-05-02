<?php declare(strict_types=1);

namespace Yireo\LokiAdminComponents\Component\Form\FormRepository;

use Magento\Framework\Message\Manager;
use Yireo\LokiAdminComponents\Component\Form\FormRepository;

class DuplicateAction implements ActionInterface
{
    public function __construct(
        private Manager $messageManager
    ) {
    }

    public function execute(FormRepository $formRepository, array $value): void
    {
        $primaryKey = $formRepository->getPrimaryKey();
        $dataProvider = $formRepository->getDataProvider();

        $id = (int)$value['item'][$primaryKey];
        $item = $dataProvider->getItem($id);

        $dataProvider->duplicate($item);
        $this->messageManager->addSuccessMessage('Item has been duplicated');
    }
}
