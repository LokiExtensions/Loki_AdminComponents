<?php
declare(strict_types=1);

namespace Loki\AdminComponents\Form\Field\FieldsResolver;

use Loki\AdminComponents\Component\Form\FormRepository;
use Loki\AdminComponents\Component\Grid\GridRepository;
use Loki\AdminComponents\Exception\NoProviderException;
use Loki\AdminComponents\Provider\FormProviderInterface;
use Loki\Components\Component\ComponentRepository;

class ProviderFieldsResolver implements FieldsResolverInterface
{
    public function resolve(ComponentRepository $repository, array $fields = []): array
    {
        if (false === $repository instanceof GridRepository && false === $repository instanceof FormRepository) {
            return $fields;
        }

        try {
            $provider = $repository->getProvider();
        } catch (NoProviderException $e) {
            return $fields;
        }

        if ($repository instanceof FormRepository && $provider instanceof FormProviderInterface) {
            foreach ($provider->getForm()->getFields() as $fieldIndex => $field) {
                $fields[$fieldIndex] = $field;
            }
        }

        return $fields;
    }
}
