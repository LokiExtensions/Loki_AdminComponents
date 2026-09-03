<?php
declare(strict_types=1);

namespace Loki\AdminComponents\Ui\Button\ButtonsResolver;

use Loki\Components\Component\ComponentRepository;

interface ButtonsResolverInterface
{
    public function resolve(ComponentRepository $repository, array $buttons = []): array;
}
