<?php declare(strict_types=1);

namespace Yireo\LokiAdminComponents\Grid\MassAction;

class DisableMassAction extends AbstractMassAction
{
    public function getLabel(): string
    {
        return 'Disable';
    }

    public function getUrl(): string
    {
        return $this->urlFactory->create()->getUrl('catalog/product/massStatus', ['status' => 0]);
    }
}
