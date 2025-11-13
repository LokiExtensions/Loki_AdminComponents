<?php declare(strict_types=1);

namespace Loki\AdminComponents\ViewModel\Form\Field;

use Magento\Framework\View\Element\Block\ArgumentInterface;

class Editor implements ArgumentInterface
{
    public function __construct(
        private array $toolbar = [],
        private array $plugins = []
    ) {
    }

    public function getToolbar(): array
    {
        return $this->toolbar;
    }

    public function getToolbarAsString(): string
    {
        $toolbar = [];
        foreach ($this->getToolbar() as $toolbarGroup) {
            if (is_array($toolbarGroup) && !empty($toolbarGroup)) {
                $toolbarGroup = implode(' ', $toolbarGroup);
            }

            if (false === is_string($toolbarGroup)) {
                continue;
            }

            $toolbar[] = $toolbarGroup;
        }

        return implode(' | ', $toolbar);

    }

    public function getPlugins(): array
    {
        $plugins = [];
        foreach ($this->plugins as $plugin) {
            if (is_string($plugin)) {
                $plugins[] = $plugin;
            }
        }

        return $plugins;
    }

    public function getPluginsAsJson(): string
    {
        return json_encode(array_values($this->getPlugins()));
    }

    public function getContentStyle(): string
    {
        return 'body { font-family:Helvetica,Arial,sans-serif; font-size:16px }';
    }
}
