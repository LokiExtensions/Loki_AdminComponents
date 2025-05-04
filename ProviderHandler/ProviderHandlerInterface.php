<?php
declare(strict_types=1);

namespace Yireo\LokiAdminComponents\ProviderHandler;

use Magento\Framework\DataObject;
use Yireo\LokiAdminComponents\Grid\State as GridState;

interface ProviderHandlerInterface
{
    public function getItem($provider, string|int $identifier): DataObject;

    public function saveItem($provider, DataObject $item);

    public function getItems($provider, GridState $gridState): array;
}
