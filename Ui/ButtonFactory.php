<?php declare(strict_types=1);

namespace Yireo\LokiAdminComponents\Ui;

use Magento\Framework\ObjectManagerInterface;

class  ButtonFactory
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
    ): Button {
        if ($primary) {
            $cssClass .= ' primary';
        }

        return $this->objectManager->create(Button::class, [
            'alpineMethod' => $alpineMethod,
            'label' => $label,
            'cssClass' => $cssClass,
        ]);
    }

    public function createNewAction(bool $primary = true): Button
    {
        return $this->create('newAction', 'New', 'new', $primary);
    }

    public function createCloseAction(bool $primary = false): Button
    {
        return $this->create('closeAction', 'Back', 'back', $primary);
    }

    public function createDeleteAction(bool $primary = false): Button
    {
        return $this->create('deleteAction', 'Delete', 'delete', $primary);
    }

    public function createSaveContinueAction(bool $primary = false): Button
    {
        return $this->create('saveAndContinueAction', 'Save & Continue', 'save', $primary);
    }


    public function createSaveDuplicateAction(bool $primary = false): Button
    {
        return $this->create('saveAndDuplicateAction', 'Save & Duplicate', 'save', $primary);
    }


    public function createSaveCloseAction(bool $primary = true): Button
    {
        return $this->create('saveAndCloseAction', 'Save & Close', 'save', $primary);
    }
}
