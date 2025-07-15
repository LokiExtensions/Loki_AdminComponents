<?php
declare(strict_types=1);

namespace Loki\AdminComponents\Form\Field;

interface FieldTypeInterface
{
    public function getTemplate(): string;
}
