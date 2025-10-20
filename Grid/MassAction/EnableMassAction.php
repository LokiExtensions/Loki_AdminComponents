<?php declare(strict_types=1);

namespace Loki\AdminComponents\Grid\MassAction;

class EnableMassAction extends MassAction
{
    protected function getUrlParameters(): array
    {
        return ['status' => 1];
    }
}
