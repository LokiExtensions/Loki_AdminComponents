<?php
declare(strict_types=1);

namespace Loki\AdminComponents\ProviderHandler;

use Magento\Framework\DataObject;
use Loki\AdminComponents\Grid\State as GridState;

interface ProviderHandlerInterface
{
    public function match(object $provider): bool;

    public function allowActions(object $provider): bool;

    public function getItem(object $provider, string|int $identifier): DataObject;

    public function createItem(object $provider, object|null $factory): DataObject;

    public function getItems(object $provider, GridState $gridState): array;

    public function saveItem(object $provider, DataObject $item);

    public function deleteItem(object $provider, DataObject $item);

    public function duplicateItem(object $provider, DataObject $item);

    public function getColumns(object $provider): array;
}
