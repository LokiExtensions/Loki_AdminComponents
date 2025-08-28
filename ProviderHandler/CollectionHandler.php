<?php
declare(strict_types=1);

namespace Loki\AdminComponents\ProviderHandler;

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DataObject;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb as AbstractResourceModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\ObjectManagerInterface;
use RuntimeException;
use Loki\AdminComponents\Grid\State as GridState;

class CollectionHandler implements ProviderHandlerInterface
{
    public function __construct(
        private ObjectManagerInterface $objectManager
    ) {
    }

    public function match(object $provider): bool
    {
        return $provider instanceof AbstractCollection;
    }

    public function getItem(object $provider, int|string $identifier): DataObject
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

    public function createItem(object $provider, object|null $factory): DataObject
    {
        if (false === $provider instanceof AbstractDb) {
            throw new RuntimeException('Provider is not an instance of '.AbstractDb::class);
        }

        $modelName = $provider->getModelName();
        if (empty($modelName)) {
            throw new RuntimeException('No model-name found in collection');
        }

        return $this->objectManager->create($modelName);
    }

    public function getItems(object $provider, GridState $gridState): array
    {
        if (false === $provider instanceof AbstractDb) {
            throw new RuntimeException('Provider is not an instance of '.AbstractDb::class);
        }

        $provider->setPageSize($gridState->getLimit());
        $provider->setCurPage($gridState->getPage());

        $sortField = $gridState->getSortBy();
        $sortDirection = $gridState->getSortDirection();
        if (!empty($sortField)) {
            $provider->setOrder($sortField, $sortDirection);
        }

        $search = $gridState->getSearch();
        if (!empty($search)) {
            foreach ($gridState->getSearchableFields() as $searchableField) {
                $provider->addFieldToFilter($searchableField, ['like' => '%'.$search.'%']);
            }
        }

        foreach ($gridState->getFilters() as $filterData) {
            if ($filterData['condition_type'] === 'eq') {
                $provider->addFieldToFilter($filterData['field'], $filterData['value']);
                continue;
            }

            $provider->addFieldToFilter($filterData['field'], [
                $filterData['condition_type'] => $filterData['value'],
            ]);
        }

        $gridState->setTotalItems($provider->getSize());

        return $provider->getItems();
    }

    public function saveItem(object $provider, DataObject $item)
    {
        /** @var AbstractResourceModel $resourceModel */
        /** @var AbstractModel $item */
        $resourceModel = $this->objectManager->get($this->getResourceModelClass($provider));
        $resourceModel->save($item);
    }

    public function deleteItem(object $provider, DataObject $item)
    {
        // TODO: Implement deleteItem() method.
    }

    public function duplicateItem(object $provider, DataObject $item)
    {
        // TODO: Implement duplicateItem() method.
    }

    public function getColumns(object $provider): array
    {
        /** @var AbstractDb $provider */
        $fields = $provider->getResource()->getUniqueFields();

        // @todo: Use this to fetch database columns
        return [];
    }

    public function getModelClass(object $provider): bool|string
    {
        /** @var AbstractDb $provider */
        return $provider->getItemObjectClass();
    }

    public function getResourceModelClass(object $provider): bool|string
    {
        /** @var AbstractDb $provider */
        return get_class($provider->getResource());
    }
}
