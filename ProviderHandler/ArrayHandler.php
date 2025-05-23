<?php
declare(strict_types=1);

namespace Yireo\LokiAdminComponents\ProviderHandler;

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DataObject;
use Magento\Framework\DataObjectFactory;
use RuntimeException;
use Yireo\LokiAdminComponents\Grid\State as GridState;
use Yireo\LokiAdminComponents\Provider\ArrayProviderInterface;

class ArrayHandler implements ProviderHandlerInterface
{
    public function __construct(
        private DataObjectFactory $dataObjectFactory,
    ) {
    }

    public function match($provider): bool
    {
        return $provider instanceof ArrayProviderInterface;
    }

    public function allowActions($provider): bool
    {
        return false;
    }

    public function getItem($provider, int|string $identifier): DataObject
    {
        throw new RuntimeException('Unable to retrieve item from array');
    }

    public function createItem(object $provider, object|null $factory): DataObject
    {
        throw new RuntimeException('Unable to retrieve item from array');
    }

    public function getItems($provider, GridState $gridState): array
    {
        /** @var ArrayProviderInterface $provider */
        $rows = $provider->getData();
        $items = [];

        foreach ($rows as $row) {
            $items[] = $this->dataObjectFactory->create()->setData($row);
        }


        $search = $gridState->getSearch();
        if (!empty($search)) {
            $items = array_filter($items, function (DataObject $item) use ($search) {
                foreach ($item->getData() as $itemValue) {
                    if (is_string($itemValue) && str_contains($itemValue, $search)) {
                        return true;
                    }
                }

                return false;
            });
        }

        $sortField = $gridState->getSortBy();
        $sortDirection = $gridState->getSortDirection();
        if (!empty($sortField)) {
            usort($items, function (DataObject $a, DataObject $b) use ($sortField) {
                $valueA = $a->getData($sortField);
                $valueB = $b->getData($sortField);
                if (is_string($valueA)) {
                    return strcmp($valueA, $valueB);
                }

                if (is_array($valueA) || is_array($valueB)) {
                    return 0;
                }

                if (is_object($valueA) || is_object($valueB)) {
                    return 0;
                }

                return $valueA <=> $valueB;
            });

            if ($sortDirection === AbstractDb::SORT_ORDER_DESC) {
                $items = array_reverse($items);
            }
        }

        $gridState->setTotalItems(count($items));
        $limit = $gridState->getLimit();
        $page = $gridState->getPage();
        $items = array_splice($items, ($page - 1) * $limit, $limit);

        return $items;
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

    public function getColumns($provider): array
    {
        /** @var ArrayProviderInterface $provider */
        return $provider->getColumns();
    }
}
