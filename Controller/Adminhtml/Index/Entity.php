<?php declare(strict_types=1);

namespace Loki\AdminComponents\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\PageFactory as ResultPageFactory;

class Entity extends Action implements HttpGetActionInterface
{
    public const ADMIN_RESOURCE = 'Loki_AdminComponents::entity';

    public function __construct(
        Context $context,
        private ResultPageFactory $resultPageFactory,
    ) {
        parent::__construct($context);
    }

    public function execute(): Page
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getLayout()->getUpdate()->addHandle('loki_admin_components_entity');

        return $resultPage;
    }
}
