<?php
declare(strict_types=1);

namespace Yireo\LokiAdminComponents\Form\Field;

interface FieldTypeInterface
{
    public function getTemplate(): string;
}
