<?php
declare(strict_types=1);

namespace Loki\AdminComponents\Grid\Filter;

interface StaticFilterInterface extends FilterInterface
{
    public function getValue(): string;
}
