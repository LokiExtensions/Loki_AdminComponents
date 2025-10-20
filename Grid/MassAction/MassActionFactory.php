<?php declare(strict_types=1);

namespace Loki\AdminComponents\Grid\MassAction;

use Magento\Framework\ObjectManagerInterface;

class MassActionFactory
{
    public function __construct(
        private ObjectManagerInterface $objectManager
    ) {
    }

    public function create(
        string $label,
        string $url,
        array $urlParameters = [],
        array $data = []
    ): MassActionInterface {
        return $this->objectManager->create(MassAction::class, [
            'label' => $label,
            'url' => $url,
            'urlParameters' => $urlParameters,
            'data' => $data,
        ]);
    }
}
