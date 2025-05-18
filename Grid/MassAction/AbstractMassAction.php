<?php declare(strict_types=1);

namespace Yireo\LokiAdminComponents\Grid\MassAction;

use Magento\Framework\UrlFactory;

abstract class AbstractMassAction implements MassActionInterface
{
    public function __construct(
        protected UrlFactory $urlFactory
    ) {
    }
}
