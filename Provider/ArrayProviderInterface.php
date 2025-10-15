<?php
declare(strict_types=1);

namespace Loki\AdminComponents\Provider;

use Loki\AdminComponents\Grid\Column\Column;

interface ArrayProviderInterface
{
    /**
     * @return Column[]
     */
    public function getColumns(): array;
    public function getData(): array;
}
