<?php
declare(strict_types=1);

namespace Loki\AdminComponents\ViewModel\Options;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\Data\OptionSourceInterface;

class YesNoOptions implements ArgumentInterface, OptionSourceInterface
{
    public function toOptionArray(): array
    {
        $options = [];


        $options[] = [
            'label' => __('Yes'),
            'value' => 1,
        ];


        $options[] = [
            'label' => __('No'),
            'value' => 0,
        ];

        return $options;
    }
}
