<?php declare(strict_types=1);

namespace Loki\AdminComponents\Ui\Button\ButtonsResolver;

use Loki\AdminComponents\Acl\ActionAuthorization;
use Loki\Components\Component\ComponentRepository;

class AclButtonsResolver implements ButtonsResolverInterface
{
    public function __construct(
        private readonly ActionAuthorization $actionAuthorization
    ) {
    }

    public function resolve(ComponentRepository $repository, array $buttons = []): array
    {
        $block = $repository->getViewModel()->getBlock();
        if ($block === null) {
            return $buttons;
        }

        return array_filter(
            $buttons,
            fn ($button) => $this->actionAuthorization->isButtonAllowed($block, $button)
        );
    }
}
