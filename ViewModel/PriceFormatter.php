<?php declare(strict_types=1);

namespace Yireo\LokiAdminComponents\ViewModel;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class PriceFormatter implements ArgumentInterface
{
    public function __construct(
        private readonly PriceCurrencyInterface $priceCurrency
    ) {
    }

    public function format($price): string
    {
        return $this->priceCurrency->format($price);
    }
}
