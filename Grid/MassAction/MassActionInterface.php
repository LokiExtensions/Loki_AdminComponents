<?php declare(strict_types=1);

namespace Loki\AdminComponents\Grid\MassAction;

use Magento\Framework\View\Element\Block\ArgumentInterface;

interface MassActionInterface extends ArgumentInterface
{
    public function getLabel(): string;
    public function getUrl(): string;
}
