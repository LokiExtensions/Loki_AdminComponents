<?php
declare(strict_types=1);

namespace Loki\AdminComponents\ViewModel\Options;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Store\Model\StoreManagerInterface;

class StatusOptions implements ArgumentInterface, OptionSourceInterface
{
    public function toOptionArray(): array
    {
        $options = [];


        $options[] = [
            'label' => __(''),
            'value' => '',
        ];

        $options[] = [
            'label' => __('Enabled'),
            'value' => 1,
        ];


        $options[] = [
            'label' => __('Disabled'),
            'value' => 0,
        ];

        return $options;
    }
}
