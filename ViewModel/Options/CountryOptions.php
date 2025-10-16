<?php declare(strict_types=1);

namespace Loki\AdminComponents\ViewModel\Options;

use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class CountryOptions implements ArgumentInterface, OptionSourceInterface
{
    public function __construct(
        private readonly DirectoryHelper $directoryHelper,
    ) {
    }

    public function toOptionArray()
    {
        $options = [];
        $options[] = [
            'value' => '',
            'label' => '',
        ];

        foreach ($this->directoryHelper->getCountryCollection() as $country) {
            $options[] = [
                'value' => $country->getCountryId(),
                'label' => $country->getName(),
            ];

        }

        asort($options);

        return $options;
    }
}
