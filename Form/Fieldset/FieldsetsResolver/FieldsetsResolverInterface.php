<?php
declare(strict_types=1);

namespace Loki\AdminComponents\Form\Fieldset\FieldsetsResolver;

use Loki\Components\Component\ComponentRepository;
use Loki\AdminComponents\Form\Fieldset\Fieldset;

interface FieldsetsResolverInterface
{
    /**
     * @param ComponentRepository $repository
     * @param Fieldset[] $fieldsets
     * @return Fieldset[]
     */
    public function resolve(ComponentRepository $repository, array $fieldsets = []): array;
}
