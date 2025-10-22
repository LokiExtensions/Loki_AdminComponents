<?php
declare(strict_types=1);

namespace Loki\AdminComponents\Grid\Filter;

use Magento\Framework\View\Element\Block\ArgumentInterface;

interface StaticFilterInterface extends ArgumentInterface
{
    public function getValue(): mixed;
    public function getCode(): string;
    public function getConditionType(): string;
}

