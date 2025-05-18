<?php declare(strict_types=1);

namespace Yireo\LokiAdminComponents\Grid\MassAction;

interface MassActionInterface
{
    public function getLabel(): string;
    public function getUrl(): string;
}
