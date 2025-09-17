<?php declare(strict_types=1);

namespace Loki\AdminComponents\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\StoreManager;

class StoreView implements ArgumentInterface
{
    public function __construct(
        private StoreManager $storeManager,
    ) {
    }

    public function getStoreHierarchy(array $storeIds): array
    {
        $websites = $this->getDefaultHierarchy();

        foreach ($storeIds as $storeId) {
            $store = $this->storeManager->getStore($storeId);
            $websites[$store->getWebsiteId()]['selected'] = true;
            $websites[$store->getWebsiteId()]['groups'][$store->getStoreGroupId()]['selected'] = true;
            $websites[$store->getWebsiteId()]['groups'][$store->getStoreGroupId()]['stores'][$storeId]['selected'] = true;
        }

        return $websites;
    }

    // @todo: Perhaps cache this?
    private function getDefaultHierarchy(): array
    {
        $websites = [];
        foreach ($this->storeManager->getWebsites() as $website) {
            $groups = [];
            foreach ($website->getGroups() as $group) {
                $stores = [];
                foreach ($this->storeManager->getStores() as $store) {
                    if ($store->getStoreGroupId() !== $group->getId()) {
                        continue;
                    }

                    $stores[$store->getId()] = [
                        'selected' => false,
                        'id' => $store->getId(),
                        'code' => $store->getCode(),
                        'name' => $store->getName(),
                    ];
                }

                $groups[$group->getId()] = [
                    'selected' => false,
                    'id' => $group->getId(),
                    'code' => $group->getCode(),
                    'name' => $group->getName(),
                    'stores' => $stores,
                ];
            }

            $websites[$website->getId()] = [
                'selected' => false,
                'id' => $website->getId(),
                'code' => $website->getCode(),
                'name' => $website->getName(),
                'groups' => $groups,
            ];
        }

        return $websites;
    }
}
