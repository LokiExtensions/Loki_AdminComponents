<?php
declare(strict_types=1);

namespace Yireo\LokiAdminComponents\ProviderHandler;

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DataObject;
use RuntimeException;
use Yireo\LokiAdminComponents\Grid\State as GridState;

class CollectionHandler implements ProviderHandlerInterface
{

    public function getItem($provider, int|string $identifier): DataObject
    {
        if (false === $provider instanceof AbstractDb) {
            throw new RuntimeException('Provider is not an instance of '.AbstractDb::class);
        }

        $idFieldName = $provider->getResource()->getIdFieldName();
        $provider->addFieldToFilter($idFieldName, $identifier);
        $provider->setPageSize(1);
        $provider->setCurPage(1);

        return $provider->getFirstItem();
    }

    public function getItems($provider, GridState $gridState): array
    {
        if (false === $provider instanceof AbstractDb) {
            throw new RuntimeException('Provider is not an instance of '.AbstractDb::class);
        }

        $provider->setPageSize($gridState->getLimit());
        $provider->setCurPage($gridState->getPage());

        $search = $gridState->getSearch();
        if (!empty($search)) {
            foreach ($gridState->getSearchableFields() as $searchableField) {
                $provider->addFieldToFilter($searchableField, ['like' => '%'.$search.'%']);
            }
        }

        $gridState->setTotalItems($provider->getSize());

        return $provider->getItems();
    }

    public function saveItem($provider, DataObject $item)
    {
        // TODO: Implement saveItem() method.
    }

    public function deleteItem($provider, DataObject $item)
    {
        // TODO: Implement deleteItem() method.
    }

    public function duplicateItem($provider, DataObject $item)
    {
        // TODO: Implement duplicateItem() method.
    }

    public function getColumns($provider): array
    {
        return [];
    }
}
