<?php declare(strict_types=1);

namespace Loki\AdminComponents\ViewModel;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class ProductImage implements ArgumentInterface
{
    public function __construct(
        private ImageHelper $imageHelper
    ) {
    }

    public function getThumbnailUrl(ProductInterface $product): string
    {
        return $this->imageHelper->init($product, 'product_page_image_small')
            ->setImageFile($product->getImage()) // image,small_image,thumbnail
            ->resize(80)
            ->getUrl();
    }

    public function getImageUrl(ProductInterface $product): string
    {
        return $this->imageHelper->init($product, 'product_page_image_medium')
            ->setImageFile($product->getImage())
            ->resize(800)
            ->getUrl();
    }
}
