<?php declare(strict_types=1);

namespace Yireo\LokiAdminComponents\Grid\Action;

class ActionListing
{
    public function __construct(
        private array $actions,
    ) {
    }

    /**
     * @return ActionInterface[]
     */
    public function getActions(): array
    {
        return $this->actions;
    }
}
