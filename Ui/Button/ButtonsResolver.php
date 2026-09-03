<?php
declare(strict_types=1);

namespace Loki\AdminComponents\Ui\Button;

use Loki\AdminComponents\Component\Form\FormRepository;
use Loki\AdminComponents\Component\Grid\GridRepository;
use Loki\AdminComponents\Ui\Button\ButtonsResolver\ButtonsResolverInterface;

class ButtonsResolver
{
    public function __construct(
        /** @var ButtonsResolverInterface[] */ private array $buttonsResolvers = []
    ) {
    }

    /**
     * @return Button[]
     */
    public function resolve(FormRepository|GridRepository $repository): array
    {
        $buttons = [];
        foreach ($this->buttonsResolvers as $buttonsResolver) {
            $buttons = $buttonsResolver->resolve($repository, $buttons);
        }

        return $buttons;
    }
}
