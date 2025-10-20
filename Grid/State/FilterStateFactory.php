<?php declare(strict_types=1);

namespace Loki\AdminComponents\Grid\State;

use Magento\Framework\ObjectManagerInterface;

class FilterStateFactory
{
    public function __construct(
        private ObjectManagerInterface $objectManager,
    ) {
    }

    public function create(
        string $field,
        mixed $value,
        string $conditionType = '',
    ): FilterState {
        return $this->objectManager->create(FilterState::class, [
            'field' => $field,
            'value' => $value,
            'conditionType' => $conditionType,
        ]);
    }
}
