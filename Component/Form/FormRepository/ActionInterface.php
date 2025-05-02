<?php declare(strict_types=1);

namespace Yireo\LokiAdminComponents\Component\Form\FormRepository;

use Yireo\LokiAdminComponents\Component\Form\FormRepository;

interface ActionInterface
{
    public function execute(FormRepository $formRepository, array $value): void;
}
