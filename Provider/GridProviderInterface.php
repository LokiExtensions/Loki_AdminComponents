<?php
declare(strict_types=1);

namespace Loki\AdminComponents\Provider;

use Loki\AdminComponents\Grid\Grid;

interface GridProviderInterface
{
    public function getGrid(): Grid;
}
