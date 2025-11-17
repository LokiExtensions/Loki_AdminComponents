<?php declare(strict_types=1);

namespace Loki\AdminComponents\Grid\Cell;

class CellAction
{
    public function __construct(
        private string $label,
        private ?string $url = null,
        private ?string $jsMethod = null,
    ) {
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function hasUrl(): bool
    {
        return !empty($this->url);
    }

    public function getUrl(): string
    {
        return (string)$this->url;
    }

    public function hasJsMethod(): bool
    {
        return !empty($this->jsMethod);
    }

    public function getJsMethod(): ?string
    {
        return (string)$this->jsMethod;
    }
}
