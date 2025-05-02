<?php declare(strict_types=1);

namespace Yireo\LokiAdminComponents\Form\FormAction;

use Magento\Framework\ObjectManagerInterface;

class FormActionFactory
{
    public function __construct(
        private ObjectManagerInterface $objectManager,
    ) {
    }

    public function create(
        string $alpineMethod,
        string $label,
        string $cssClass,
        bool $primary = false
    ): FormAction {
        if ($primary) {
            $cssClass .= ' primary';
        }

        return $this->objectManager->create(FormAction::class, [
            'alpineMethod' => $alpineMethod,
            'label' => $label,
            'cssClass' => $cssClass,
        ]);
    }

    public function createCloseAction(bool $primary = false): FormAction
    {
        return $this->create('closeAction', 'Back', 'back', $primary);
    }

    public function createDeleteAction(bool $primary = false): FormAction
    {
        return $this->create('deleteAction', 'Delete', 'delete', $primary);
    }

    public function createSaveContinueAction(bool $primary = false): FormAction
    {
        return $this->create('saveAndContinueAction', 'Save & Continue', 'save', $primary);
    }


    public function createSaveDuplicateAction(bool $primary = false): FormAction
    {
        return $this->create('saveAndDuplicateAction', 'Save & Duplicate', 'save', $primary);
    }


    public function createSaveCloseAction(bool $primary = true): FormAction
    {
        return $this->create('saveAndCloseAction', 'Save & Close', 'save', $primary);
    }
}
