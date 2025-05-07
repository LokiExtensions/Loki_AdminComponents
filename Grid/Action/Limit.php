<?php declare(strict_types=1);

namespace Yireo\LokiAdminComponents\Grid\Action;

use Yireo\LokiAdminComponents\Component\Grid\GridRepository;
use Yireo\LokiAdminComponents\Grid\State;

class Limit implements ActionInterface
{
    public function __construct(
        private State $state,
    ) {
    }

    public function execute(GridRepository $gridRepository, array $value): void
    {
        if (!isset($value['limit'])) {
            return;
        }

        $this->state->setLimit((int)$value['limit']);
    }
}
