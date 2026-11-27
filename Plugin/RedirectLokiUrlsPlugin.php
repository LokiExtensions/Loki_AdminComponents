<?php
declare(strict_types=1);

namespace Loki\AdminComponents\Plugin;

use Loki\AdminComponents\Util\RedirectUrlValidator;
use Magento\Framework\App\FrontController;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Redirect as ResultRedirect;

class RedirectLokiUrlsPlugin
{
    public function __construct(
        private RequestInterface $request,
        private RedirectUrlValidator $redirectUrlValidator,
    ) {
    }

    public function afterDispatch(FrontController $subject, $result)
    {
        if (false === $result instanceof ResultRedirect) {
            return $result;
        }

        if (false === $this->request instanceof HttpRequest || false === $this->request->isPost()) {
            return $result;
        }

        if (empty($this->request->getParam('namespace'))) {
            return $result;
        }

        $lokiRedirectUrl = $this->request->getParam('loki_redirect_url');
        if (empty($lokiRedirectUrl)) {
            return $result;
        }

        if (false === $this->redirectUrlValidator->isValid($lokiRedirectUrl)) {
            return $result;
        }

        $result->setUrl($lokiRedirectUrl);
        return $result;
    }
}
