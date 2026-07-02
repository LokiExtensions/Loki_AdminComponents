<?php
declare(strict_types=1);

namespace Loki\AdminComponents\Provider;

use Loki\AdminComponents\Form\Form;

interface FormProviderInterface
{
    public function getForm(): Form;
}
