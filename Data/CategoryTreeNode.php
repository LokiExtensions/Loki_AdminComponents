<?php

declare(strict_types=1);

namespace Loki\AdminComponents\Data;

class CategoryTreeNode
{
    public function __construct(
        public readonly int $id,
        public readonly string $label,
        public readonly bool $isActive,
        public readonly int $level,
        public readonly ?int $parentId = null,
        public readonly array $parentIds = [],
        public readonly array $children = [],
    ) {
    }

    public function toUiArray(): array
    {
        return array_merge(
            get_object_vars($this),
            [
                'hasChildren' => $this->children !== [],
                'expanded' => $this->level === 0,
                'hovered' => false,
                'parentsPath' => null,
            ]
        );
    }
}
