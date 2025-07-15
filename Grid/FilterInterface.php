<?php
declare(strict_types=1);

namespace Loki\AdminComponents\Grid;

use Magento\Framework\View\Element\Block\ArgumentInterface;

interface FilterInterface extends ArgumentInterface
{
    public function getField(): string;

    public function getValue(): mixed;

    public function getConditionType(): string;
}
