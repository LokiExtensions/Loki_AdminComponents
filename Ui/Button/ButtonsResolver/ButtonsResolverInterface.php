<?php
declare(strict_types=1);

namespace Loki\AdminComponents\Ui\Button\ButtonsResolver;

use Loki\AdminComponents\Ui\Button\ButtonInterface;
use Loki\Components\Component\ComponentRepository;

interface ButtonsResolverInterface
{
    /**
     * @param ComponentRepository $repository
     * @param ButtonInterface[] $buttons
     * @return ButtonInterface[]
     */
    public function resolve(ComponentRepository $repository, array $buttons = []): array;
}
