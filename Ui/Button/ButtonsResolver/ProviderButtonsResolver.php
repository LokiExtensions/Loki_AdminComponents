<?php
declare(strict_types=1);

namespace Loki\AdminComponents\Ui\Button\ButtonsResolver;

use Loki\AdminComponents\Component\Form\FormRepository;
use Loki\AdminComponents\Component\Grid\GridRepository;
use Loki\AdminComponents\Provider\ButtonsProviderInterface;
use Loki\AdminComponents\Provider\FormProviderInterface;
use Loki\Components\Component\ComponentRepository;

class ProviderButtonsResolver implements ButtonsResolverInterface
{
    public function resolve(ComponentRepository $repository, array $buttons = []): array
    {
        if (false === $repository instanceof GridRepository && false === $repository instanceof FormRepository) {
            return $buttons;
        }

        $provider = $repository->getProvider();
        if ($provider instanceof FormProviderInterface) {
            foreach ($provider->getForm()->getButtons() as $buttonIndex => $button) {
                $buttons[$buttonIndex] = $button;
            }
        }

        if ($provider instanceof ButtonsProviderInterface) {
            foreach ($provider->getButtons() as $buttonIndex => $button) {
                $buttons[$buttonIndex] = $button;
            }
        }

        return $buttons;
    }
}
