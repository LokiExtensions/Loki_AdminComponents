<?php
declare(strict_types=1);

namespace Loki\AdminComponents\ViewModel\Options;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrderFactory;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\Data\OptionSourceInterface;

class ProductOptions implements ArgumentInterface, OptionSourceInterface
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private SearchCriteriaBuilder $searchCriteriaBuilder,
        private SortOrderFactory $sortOrderFactory
    ) {
    }

    public function toOptionArray()
    {
        $options = [];

        $sortOrder = $this->sortOrderFactory->create();
        $sortOrder->setField('name');
        $this->searchCriteriaBuilder->addSortOrder($sortOrder);

        $searchCriteria = $this->searchCriteriaBuilder->create();
        foreach ($this->productRepository->getList($searchCriteria)->getItems() as $product) {
            $options[] = [
                'value' => $product->getId(),
                'label' => $product->getName(),
            ];
        }

        return $options;
    }
}
