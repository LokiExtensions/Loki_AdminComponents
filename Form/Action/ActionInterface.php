<?php declare(strict_types=1);

namespace Loki\AdminComponents\Form\Action;

use Loki\AdminComponents\Component\Form\FormRepository;

interface ActionInterface
{
    public function execute(FormRepository $formRepository, array $value): void;
}
