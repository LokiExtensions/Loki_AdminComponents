<?php
declare(strict_types=1);

namespace Loki\AdminComponents\Provider;

use Loki\AdminComponents\Form\Form;
use Loki\AdminComponents\Grid\Column\Column;

interface FormProviderInterface
{
    /**
     * @return Column[]
     */
    public function getForm(): Form;
    public function getData(): array;
}
