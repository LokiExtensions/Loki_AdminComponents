<?php declare(strict_types=1);

namespace Loki\AdminComponents\Form;

use Magento\Framework\ObjectManagerInterface;

class FormFactory
{
    public function __construct(
        private ObjectManagerInterface $objectManager,
    ) {
    }

    public function create(string $code): Form
    {
        return $this->objectManager->create(Form::class, [
            'code' => $code,
        ]);
    }
}
