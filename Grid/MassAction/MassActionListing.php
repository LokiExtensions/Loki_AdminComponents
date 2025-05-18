<?php declare(strict_types=1);

namespace Yireo\LokiAdminComponents\Grid\MassAction;

class MassActionListing
{
    public function __construct(
        private array $massActions = [],
    ) {
    }

    /**
     * @return MassActionInterface[]
     */
    public function getMassActions(): array
    {
        return $this->massActions;
    }
}
