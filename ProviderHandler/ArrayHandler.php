<?php
declare(strict_types=1);

namespace Yireo\LokiAdminComponents\ProviderHandler;

use Magento\Framework\DataObject;
use RuntimeException;
use Yireo\LokiAdminComponents\Grid\State as GridState;

class ArrayHandler implements ProviderHandlerInterface
{
    public function getItem($provider, int|string $identifier): DataObject
    {
        if (false === is_array($provider)) {
            throw new RuntimeException('Provider is not an array.');
        }

        if (isset($provider[$identifier])) {
            return $provider[$identifier];
        }

        throw new RuntimeException('Unable to extra identifier "'.$identifier.'" from array');
    }

    public function getItems($provider, GridState $gridState): array
    {
        // @todo: Use a new interface ArrayProvider instead
        return $provider;
    }

    public function saveItem($provider, DataObject $item)
    {
    }

    public function deleteItem($provider, DataObject $item)
    {
    }

    public function duplicateItem($provider, DataObject $item)
    {
    }
}
