<?php
declare(strict_types=1);

namespace Yireo\LokiAdminComponents\Form\Fieldset;

use Magento\Framework\ObjectManagerInterface;

class FieldTypeProvider
{
    public function __construct(
        private array $fieldTypes = []
    ) {
    }

    public function getFieldTypeByCode(string $code): FieldTypeInterface
    {
        foreach ($this->getFieldTypes() as $fieldTypeCode => $fieldType) {
            if ($fieldTypeCode === $code) {
                return $fieldType;
            }
        }

        throw new \Exception('Field type not found');
    }
    public function getFieldTypes(): array
    {
        return $this->fieldTypes;
    }
}
