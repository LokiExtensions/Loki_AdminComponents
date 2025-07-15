<?php
declare(strict_types=1);

namespace Loki\AdminComponents\Plugin;

use Magento\Framework\App\FrontController;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Redirect as ResultRedirect;

class RedirectLokiUrlsPlugin
{
    public function __construct(
        private RequestInterface $request
    ) {
    }

    public function afterDispatch(FrontController $subject, $result)
    {
        if (false === $result instanceof ResultRedirect) {
            return $result;
        }

        $lokiRedirectUrl = $this->request->getParam('loki_redirect_url');
        if (empty($lokiRedirectUrl)) {
            return $result;
        }

        $result->setUrl($lokiRedirectUrl);
        return $result;
    }
}
