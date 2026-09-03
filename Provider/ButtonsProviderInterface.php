<?php
declare(strict_types=1);

namespace Loki\AdminComponents\Provider;

use Loki\AdminComponents\Ui\Button\ButtonInterface;

interface ButtonsProviderInterface
{
    /**
     * @return ButtonInterface[]
     */
    public function getButtons(): array;
}
