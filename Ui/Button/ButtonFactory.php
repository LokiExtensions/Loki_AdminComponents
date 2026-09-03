<?php declare(strict_types=1);

namespace Loki\AdminComponents\Ui\Button;

use Magento\Framework\ObjectManagerInterface;

class ButtonFactory
{
    public function __construct(
        private ObjectManagerInterface $objectManager,
    ) {
    }

    public function create(
        string $id,
        string $method,
        string $label,
        string $cssClass = '',
        string $url = '',
        array $subButtons = [],
        bool $primary = false,
    ): Button {
        if ($primary) {
            $cssClass .= ' primary';
        }

        return $this->objectManager->create(Button::class, [
            'id' => $id,
            'label' => $label,
            'cssClass' => $cssClass,
            'method' => $method,
            'url' => $url,
            'subButtons' => $subButtons,
        ]);
    }

    public function createLinkAction(
        string $url,
        string $label,
        string $cssClass = '',
        array $subButtons = [],
        bool $primary = false,
    ): Button {
        return $this->create(
            'create',
            'redirectAction',
            $label,
            $cssClass,
            $url,
            $subButtons,
            $primary
        );
    }

    public function createNewAction(bool $primary = true, string $label = 'New'): Button
    {
        return $this->create('new', 'newAction', $label, 'new', primary: $primary);
    }

    public function createCloseAction(bool $primary = false): Button
    {
        return $this->create('close', 'closeAction', 'Back', 'back', primary: $primary);
    }

    public function createDeleteAction(bool $primary = false): Button
    {
        return $this->create('delete', 'deleteAction', 'Delete', 'delete', primary: $primary);
    }

    public function createSaveContinueAction(bool $primary = false): Button
    {
        return $this->create('saveAndContinue', 'saveAndContinueAction', 'Save & Continue', 'save', primary: $primary);
    }


    public function createSaveDuplicateAction(bool $primary = false): Button
    {
        return $this->create('saveAndDuplicate', 'saveAndDuplicateAction', 'Save & Duplicate', 'save', primary: $primary);
    }


    public function createSaveCloseAction(bool $primary = true): Button
    {
        return $this->create('saveAndClose', 'saveAndCloseAction', 'Save & Close', 'save', primary: $primary);
    }
}
