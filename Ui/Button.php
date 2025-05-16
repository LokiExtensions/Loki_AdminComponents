<?php declare(strict_types=1);

namespace Yireo\LokiAdminComponents\Ui;

class Button
{
    public function __construct(
        private string $alpineMethod,
        private string $label,
        private string $cssClass,
        private array $subButtons = []
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

    /**
     * @return Button[]
     */
    public function getSubButtons(): array
    {
        return $this->subButtons;
    }
}
