<?php
declare(strict_types=1);

namespace Loki\AdminComponents\Form\Field\FieldsResolver;

use Loki\AdminComponents\Component\Form\FormRepository;
use Loki\AdminComponents\Component\Grid\GridRepository;
use Loki\AdminComponents\Provider\FormProviderInterface;
use Loki\Components\Component\ComponentRepository;

class BlockFieldsResolver implements FieldsResolverInterface
{
    public function resolve(ComponentRepository $repository, array $fields = []): array
    {
        if (false === $repository instanceof GridRepository && false === $repository instanceof FormRepository) {
            return $fields;
        }

        $provider = $repository->getProvider();
        if ($repository instanceof FormRepository && $provider instanceof FormProviderInterface) {
            foreach ($provider->getForm()->getFields() as $fieldIndex => $field) {
                $fields[$fieldIndex] = $field;
            }
        }

        return $fields;
    }
}
