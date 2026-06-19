<?php declare(strict_types=1);

namespace Loki\AdminComponents\Controller\Adminhtml\Index;

use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\PageFactory as ResultPageFactory;

class Entity implements HttpGetActionInterface
{
    const ADMIN_RESOURCE = 'Loki_AdminComponents::entity';

    public function __construct(
        private ResultPageFactory $resultPageFactory,
    ) {
    }

    public function execute(): Page
    {
        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getLayout()->getUpdate()->addHandle('loki_admin_components_entity');

        return $resultPage;
    }
}
