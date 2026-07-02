<?php
declare(strict_types=1);

namespace Loki\AdminComponents\ProviderHandler;

use Loki\AdminComponents\Provider\FormProviderInterface;
use Magento\Framework\DataObject;
use Magento\Framework\DataObjectFactory;
use Loki\AdminComponents\Grid\State as GridState;

class FormHandler implements ProviderHandlerInterface
{
    public function __construct(
        private DataObjectFactory $dataObjectFactory,
    ) {
    }

    public function match(object $provider): bool
    {
        return $provider instanceof FormProviderInterface;
    }

    public function getItem(object $provider, int|string $identifier): DataObject
    {
        /** @var FormProviderInterface $provider */
        return $provider->getItem($identifier);
    }

    public function createItem(object $provider, object|null $factory): DataObject
    {
        /** @var FormProviderInterface $provider */
        return $this->dataObjectFactory->create();
    }

    public function getItems(object $provider, GridState $gridState): array
    {
        return [];
    }

    public function saveItem(object $provider, DataObject $item)
    {
        /** @var FormProviderInterface $provider */
        $provider->saveItem($item);
    }

    public function deleteItem(object $provider, DataObject $item)
    {
        /** @var FormProviderInterface $provider */
        $provider->deleteItem($item);
    }

    public function duplicateItem(object $provider, DataObject $item)
    {
        /** @var FormProviderInterface $provider */
        $provider->duplicateItem($item);
    }

    public function getColumns(object $provider): array
    {
        return [];
    }

    public function getModelClass(object $provider): bool|string
    {
        return false;
    }

    public function getResourceModelClass(object $provider): bool|string
    {
        return false;
    }

    public function getPrimaryKey(object $provider): ?string
    {
        return null;
    }
}
