<?php declare(strict_types=1);

namespace Loki\AdminComponents\Ui\Button;

interface ButtonInterface
{
    public function getMethod(): string;

    public function getLabel(): string;

    public function getCssClass(): string;

    /**
     * @return Button[]
     */
    public function getSubButtons(): array;

    public function getUrl(): string;

    public function setId(string $id): void;

    public function setMethod(string $method): void;

    public function setLabel(string $label): void;

    public function setCssClass(string $cssClass): void;

    public function setUrl(string $url): void;

    public function setSubButtons(array $subButtons): void;
}
