<?php declare(strict_types=1);

namespace Loki\AdminComponents\Form\Action;

class ActionListing
{
    public function __construct(
        private array $actions = [],
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
