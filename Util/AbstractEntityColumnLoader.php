<?php declare(strict_types=1);

namespace Loki\AdminComponents\Util;

use Loki\AdminComponents\Grid\Column\ColumnFactory;
use Magento\Eav\Model\Entity\AbstractEntity;

class AbstractEntityColumnLoader
{
    public function __construct(
        private ColumnFactory $columnFactory
    ) {
    }

    public function getColumns(AbstractEntity $entity): array
    {
        $entity->loadAllAttributes();
        $attributes = $entity->getAttributesByCode();

        $columns = [];
        foreach ($attributes as $attributeCode => $attribute) {
            if (!$attribute->getIsVisibleInGrid()) {
                continue;
            }

            $columns[] = $this->columnFactory->create([
                'code' => $attributeCode,
                'label' => $attribute->getFrontendLabel(),
            ]);
        }

        return $columns;
    }
}
