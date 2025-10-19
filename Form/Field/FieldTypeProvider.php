<?php
declare(strict_types=1);

namespace Loki\AdminComponents\Form\Field;

use Exception;
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
            if ($fieldTypeCode !== $code) {
                continue;
            }

            if (false === $fieldType instanceof FieldTypeInterface) {
                throw new Exception('Field type "'.$code.'" is not an instance of '.FieldTypeInterface::class);
            }

            return $fieldType;
        }

        throw new Exception('Field type "'.$code.'" not found');
    }
    public function getFieldTypes(): array
    {
        return $this->fieldTypes;
    }
}
