<?php declare(strict_types=1);

namespace Loki\AdminComponents\Grid\Action;

use Loki\AdminComponents\Component\Grid\GridRepository;

interface ActionInterface
{
    public function execute(GridRepository $gridRepository, array $value): void;
}
