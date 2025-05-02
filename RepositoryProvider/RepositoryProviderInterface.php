<?php declare(strict_types=1);

namespace Yireo\LokiAdminComponents\RepositoryProvider;

use Magento\Framework\DataObject;

interface RepositoryProviderInterface
{
    public function getRepository();
    public function create(): DataObject;
}
