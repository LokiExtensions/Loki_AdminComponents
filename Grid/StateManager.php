<?php
declare(strict_types=1);

namespace Yireo\LokiAdminComponents\Grid;

use Magento\Framework\ObjectManagerInterface;

class StateManager
{
    /** @var State[] $states */
    private array $states = [];

    public function __construct(
        private ObjectManagerInterface $objectManager
    ) {
    }

    public function get(string $namespace): State
    {
        if (array_key_exists($namespace, $this->states)) {
            return $this->states[$namespace];
        }

        $state = $this->objectManager->create(State::class, [
            'namespace' => $namespace,
        ]);

        $this->states[$namespace] = $state;

        return $state;
    }
}
