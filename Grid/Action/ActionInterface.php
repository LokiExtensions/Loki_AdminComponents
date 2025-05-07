<?php declare(strict_types=1);

namespace Yireo\LokiAdminComponents\Grid\Action;

use Yireo\LokiAdminComponents\Component\Grid\GridRepository;

interface ActionInterface
{
    public function execute(GridRepository $gridRepository, array $value): void;
}
