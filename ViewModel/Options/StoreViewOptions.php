<?php
declare(strict_types=1);

namespace Loki\AdminComponents\ViewModel\Options;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Store\Model\StoreManagerInterface;

class StoreViewOptions implements ArgumentInterface, OptionSourceInterface
{
    public function __construct(
        private readonly StoreManagerInterface $storeManager
    ) {
    }

    public function toOptionArray(): array
    {
        $options = [];

        $options[] = ['label' => __('All Store Views'), 'value' => ''];

        $websites = $this->storeManager->getWebsites();
        uasort($websites, function ($a, $b) {
            $soA = (int)$a->getSortOrder();
            $soB = (int)$b->getSortOrder();

            return $soA <=> $soB ?: strcasecmp((string)$a->getName(), (string)$b->getName());
        });

        foreach ($websites as $website) {
            /** @var \Magento\Store\Model\Website $website */

            $groups = $website->getGroups();
            usort($groups, function ($a, $b) {
                return strcasecmp((string)$a->getName(), (string)$b->getName());
            });

            $groupBlocks = [];

            foreach ($groups as $group) {
                /** @var \Magento\Store\Model\Group $group */

                $stores = $group->getStores();
                usort($stores, function ($a, $b) {
                    $soA = (int)$a->getSortOrder();
                    $soB = (int)$b->getSortOrder();

                    return $soA <=> $soB ?: strcasecmp((string)$a->getName(), (string)$b->getName());
                });

                $storeOptions = [];
                foreach ($stores as $store) {
                    /** @var \Magento\Store\Model\Store $store */
                    $storeOptions[] = [
                        'label' => sprintf('%s [%s]', $store->getName(), $store->getCode()),
                        'value' => (string)$store->getId(),
                    ];
                }

                $groupBlocks[] = [
                    'label' => sprintf('%s', $group->getName()),
                    'value' => $storeOptions,
                ];
            }

            $options[] = [
                'label' => sprintf('%s [%s]', $website->getName(), $website->getCode()),
                'value' => $groupBlocks,
            ];
        }

        return $options;
    }
}
