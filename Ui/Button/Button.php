<?php declare(strict_types=1);

namespace Loki\AdminComponents\Ui\Button;

class Button implements ButtonInterface
{
    public function __construct(
        private string $id,
        private string $method,
        private string $label,
        private string $cssClass = '',
        private string $url = '',
        private array $subButtons = []
    ) {
    }

    public function getId(): string
    {
        return $this->id;
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

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function setMethod(string $method): void
    {
        $this->method = $method;
    }

    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    public function setCssClass(string $cssClass): void
    {
        $this->cssClass = $cssClass;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    public function setSubButtons(array $subButtons): void
    {
        $this->subButtons = $subButtons;
    }
}
