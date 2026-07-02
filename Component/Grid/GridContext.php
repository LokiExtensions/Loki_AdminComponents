<?php declare(strict_types=1);

namespace Loki\AdminComponents\Component\Grid;

use Loki\Components\Component\AbstractComponentContext;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\UrlFactory;

/**
 * @method UrlFactory getUrlFactory()
 * @method ManagerInterface getMessageManager()
 * @method ScopeConfigInterface getScopeConfig()
 * @method Http getRequest()
 */
class GridContext extends AbstractComponentContext
{
}
