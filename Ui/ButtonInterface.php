<?php declare(strict_types=1);

namespace Loki\AdminComponents\Ui;

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
}
