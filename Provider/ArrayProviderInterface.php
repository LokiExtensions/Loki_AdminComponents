<?php
declare(strict_types=1);

namespace Loki\AdminComponents\Provider;

interface ArrayProviderInterface
{
    public function getColumns(): array;
    public function getData(): array;
}
