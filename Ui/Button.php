<?php declare(strict_types=1);

namespace Loki\AdminComponents\Ui;

class Button
{
    public function __construct(
        private string $method,
        private string $label,
        private string $cssClass = '',
        private string $url = '',
        private array $subButtons = []
    ) {
    }

    public function getMethod(): string
    {
        return $this->method;
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

    public function getUrl(): string
    {
        return $this->url;
    }
}
