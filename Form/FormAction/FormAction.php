<?php declare(strict_types=1);

namespace Yireo\LokiAdminComponents\Form\FormAction;

class FormAction
{
    public function __construct(
        private string $alpineMethod,
        private string $label,
        private string $cssClass,
        private array $subActions = []
    ) {
    }

    public function getAlpineMethod(): string
    {
        return $this->alpineMethod;
    }
    public function getLabel(): string
    {
        return $this->label;
    }

    public function getCssClass(): string
    {
        return $this->cssClass;
    }

    public function getSubActions(): array
    {
        return $this->subActions;
    }
}
