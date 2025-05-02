<?php declare(strict_types=1);

namespace Yireo\LokiAdminComponents\Component\Form\FormRepository;

use Yireo\LokiAdminComponents\Component\Form\FormRepository;
use Yireo\LokiAdminComponents\Component\Form\FormViewModel;
use Yireo\LokiComponents\Exception\RedirectException;

class CloseAction implements ActionInterface
{
    public function execute(FormRepository $formRepository, array $value): void
    {
        /** @var FormViewModel $viewModel */
        $viewModel = $formRepository->getComponent()->getViewModel();
        throw new RedirectException($viewModel->getIndexUrl());
    }
}
