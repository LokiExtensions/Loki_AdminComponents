<?php

declare(strict_types=1);

namespace Loki\AdminComponents\Service\CategorySelection;

use Loki\AdminComponents\Data\CategoryTreeNode;
use Loki\AdminComponents\Form\Field\Field;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\LayoutInterface;

class CategoryTreeRenderer
{
    private const TEMPLATE_CHILD_ITEM =
        'Loki_AdminComponents::form/field_type/category_selection/select_inner_item.phtml';
    private const MAIN_SCRIPT =
        'Loki_AdminComponents::form/field_type/category_selection/main_js.phtml';
    private const TEMPLATE_BREADCRUMB = 'Loki_AdminComponents::form/field_type/category_selection/crumbs.phtml';
    private const TEMPLATE_SEARCH = 'Loki_AdminComponents::form/field_type/category_selection/search_input.phtml';

    public function __construct(
        private readonly LayoutInterface $layout,
    ) {
    }

    public function renderMainScript(Field $field): string
    {
        return $this->layout->createBlock(Template::class, 'category_selection_main_script')
            ->setData('field', $field)
            ->setTemplate($this->getMainScript())
            ->toHtml();
    }

    public function renderChildNode(CategoryTreeNode $categoryTreeNode, Field $field): string
    {
        return $this->layout->createBlock(Template::class, 'category_selection_inner_item_' . $categoryTreeNode->id)
            ->setData('category_tree_node', $categoryTreeNode)
            ->setData('field', $field)
            ->setTemplate($this->getChildItemTemplate())
            ->toHtml();
    }

    public function renderBreadcrumb(): string
    {
        return $this->layout->createBlock(Template::class, 'categories_selection_crumbs')
            ->setTemplate($this->getBreadcrumbsTemplate())
            ->toHtml();
    }

    public function renderSearchInput(): string
    {
        return $this->layout->createBlock(Template::class, 'category_selection_search_input')
            ->setTemplate($this->getSearchInputTemplate())
            ->toHtml();
    }

    /**
     * Make it public to provide facility to overwrite template.
     */
    public function getChildItemTemplate(): string
    {
        return self::TEMPLATE_CHILD_ITEM;
    }

    /**
     * Make it public to provide facility to overwrite template.
     */
    public function getBreadcrumbsTemplate(): string
    {
        return self::TEMPLATE_BREADCRUMB;
    }

    /**
     * Make it public to provide facility to overwrite template.
     */
    public function getSearchInputTemplate(): string
    {
        return self::TEMPLATE_SEARCH;
    }

    /**
     * Make it public to provide facility to overwrite template.
     */
    public function getMainScript(): string
    {
        return self::MAIN_SCRIPT;
    }
}
