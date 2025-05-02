<?php declare(strict_types=1);

namespace Yireo\LokiAdminComponents\Grid\Cell;

class CellAction
{
    public function __construct(
        private string $url,
        private string $label,
    ) {
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getLabel(): string
    {
        return $this->label;
    }
}
