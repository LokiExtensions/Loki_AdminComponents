<?php
declare(strict_types=1);

namespace Yireo\LokiAdminComponents\Grid;

use Magento\Framework\ObjectManagerInterface;

class StateFactory
{
    public function __construct(
        private ObjectManagerInterface $objectManager
    ) {
    }

    public function create(array $arguments = []): State
    {
        $state = $this->objectManager->create(State::class, $arguments);
        // @todo: Add data from session to current state
        return $state;
    }
}
