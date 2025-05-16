<?php
declare(strict_types=1);

namespace Yireo\LokiAdminComponents\Provider;

interface ArrayProviderInterface
{
    public function getColumns(): array;
    public function getData(): array;
}
