<?php
declare(strict_types=1);

namespace Loki\AdminComponents\Ui\Button\ButtonsResolver;

use Loki\AdminComponents\Component\Grid\GridRepository;
use Loki\Components\Component\ComponentRepository;

class DefaultGridButtonsResolver implements ButtonsResolverInterface
{
    public function resolve(ComponentRepository $repository, array $buttons = []): array
    {
        if (!empty($buttons)) {
            return $buttons;
        }

        if (false === $repository instanceof GridRepository) {
            return [];
        }

        return [];
    }
}
