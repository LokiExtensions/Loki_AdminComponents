<?php
declare(strict_types=1);

namespace Loki\AdminComponents\Form\Field\FieldsResolver;

use Loki\AdminComponents\Form\Field\Field;
use Loki\Components\Component\ComponentRepository;

interface FieldsResolverInterface
{
    /**
     * @param ComponentRepository $repository
     * @param Field[] $fields
     * @return Field[]
     */
    public function resolve(ComponentRepository $repository, array $fields = []): array;
}
