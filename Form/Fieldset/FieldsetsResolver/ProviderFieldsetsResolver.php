<?php
declare(strict_types=1);

namespace Loki\AdminComponents\Form\Fieldset\FieldsetsResolver;

use Loki\AdminComponents\Component\Form\FormRepository;
use Loki\AdminComponents\Component\Grid\GridRepository;
use Loki\AdminComponents\Provider\FormProviderInterface;
use Loki\Components\Component\ComponentRepository;

class ProviderFieldsetsResolver implements FieldsetsResolverInterface
{
    public function resolve(ComponentRepository $repository, array $fieldsets = []): array
    {
        if (false === $repository instanceof GridRepository && false === $repository instanceof FormRepository) {
            return $fieldsets;
        }

        $provider = $repository->getProvider();
        if ($repository instanceof FormRepository && $provider instanceof FormProviderInterface) {
            foreach ($provider->getForm()->getFieldsets() as $fieldsetIndex => $fieldset) {
                $fieldsets[$fieldsetIndex] = $fieldset;
            }
        }

        return $fieldsets;
    }
}
