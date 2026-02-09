<?php

declare(strict_types=1);

namespace Loki\AdminComponents\Form\Field\FieldType;

use Loki\AdminComponents\Data\CategoryTreeNode;
use Loki\AdminComponents\Service\CategorySelection\CategoryTreeBuilder;
use Loki\AdminComponents\Service\CategorySelection\CategoryTreeRenderer;
use Loki\AdminComponents\Form\Field\Field;
use Loki\AdminComponents\Form\Field\FieldType\FieldTypeAbstract;
use Magento\Framework\Serialize\SerializerInterface;

class CategorySelection extends FieldTypeAbstract
{
    public function __construct(
        private readonly CategoryTreeBuilder $categoryTreeBuilder,
        private readonly CategoryTreeRenderer $categoryTreeRenderer,
        private readonly SerializerInterface $serializer,
    ) {
    }

    public function getTemplate(): string
    {
        return 'Loki_AdminComponents::form/field_type/category_selection.phtml';
    }

    public function getCategoryNodeJson(CategoryTreeNode $treeNode): string
    {
        return $this->serializer->serialize($treeNode->toUiArray());
    }

    /**
     * @param CategoryTreeNode[] $categoryTreeNodes
     */
    public function getMaxCategoryLevel(array $categoryTreeNodes): int
    {
        return $this->categoryTreeBuilder->calculateMaxLevel($categoryTreeNodes);
    }

    public function renderMainScript(Field $field): string
    {
        return $this->categoryTreeRenderer->renderMainScript($field);
    }

    public function renderChildCategoryNode(CategoryTreeNode $categoryTreeNode, Field $field): string
    {
        return $this->categoryTreeRenderer->renderChildNode($categoryTreeNode, $field);
    }

    public function renderCategoriesBreadcrumb(): string
    {
        return $this->categoryTreeRenderer->renderBreadcrumb();
    }

    public function renderSearchInput(): string
    {
        return $this->categoryTreeRenderer->renderSearchInput();
    }

    public function getHoverSequenceJson(): string
    {
        $categoryTree = $this->getCategoryTree();
        $hoverSequence = $this->categoryTreeBuilder->buildHoverSequence($categoryTree);

        return $this->serializer->serialize($hoverSequence);
    }

    /**
     * @return CategoryTreeNode[]
     */
    public function getCategoryTree(int $storeId = 0, ?string $filter = null): array
    {
        return $this->categoryTreeBuilder->build($storeId, $filter);
    }
}
