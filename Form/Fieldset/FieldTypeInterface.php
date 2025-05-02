<?php
declare(strict_types=1);

namespace Yireo\LokiAdminComponents\Form\Fieldset;

interface FieldTypeInterface
{
    public function getTemplate(): string;
}
