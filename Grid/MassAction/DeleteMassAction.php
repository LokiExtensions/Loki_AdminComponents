<?php declare(strict_types=1);

namespace Yireo\LokiAdminComponents\Grid\MassAction;

class DeleteMassAction extends AbstractMassAction
{
    public function getLabel(): string
    {
        return 'Delete';
    }

    public function getUrl(): string
    {
        return $this->urlFactory->create()->getUrl('catalog/product/massDelete');
    }
}
