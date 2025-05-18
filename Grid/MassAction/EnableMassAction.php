<?php declare(strict_types=1);

namespace Yireo\LokiAdminComponents\Grid\MassAction;

class EnableMassAction extends AbstractMassAction
{
    public function getLabel(): string
    {
        return 'Enable';
    }

    public function getUrl(): string
    {
        return $this->urlFactory->create()->getUrl('catalog/product/massStatus', ['status' => 1]);
    }
}
