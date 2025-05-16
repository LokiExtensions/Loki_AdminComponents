<?php
declare(strict_types=1);

namespace Yireo\LokiAdminComponents\ProviderHandler;

use Magento\Framework\DataObject;
use Magento\Framework\DataObjectFactory;
use RuntimeException;
use Yireo\LokiAdminComponents\Grid\State;
use Yireo\LokiAdminComponents\Grid\State as GridState;
use Yireo\LokiAdminComponents\Provider\ArrayProviderInterface;

class ArrayHandler implements ProviderHandlerInterface
{
    public function __construct(
        private DataObjectFactory $dataObjectFactory,
        private State $state
    ) {
    }

    public function match($provider): bool
    {
        return $provider instanceof ArrayProviderInterface;
    }

    public function getItem($provider, int|string $identifier): DataObject
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


        $search = $this->state->getSearch();
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

        $this->state->setTotalItems(count($items));
        $limit = $this->state->getLimit();
        $page = $this->state->getPage();
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
