<?php
declare(strict_types=1);

namespace Loki\AdminComponents\Grid\Filter;

use Magento\Framework\View\Element\Block\ArgumentInterface;

interface FilterInterface extends ArgumentInterface
{
    public function getLabel(): string;
    public function getCode(): string;

    public function getConditionType(): string;
    public function render(): string;
}
