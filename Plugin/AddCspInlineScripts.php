<?php declare(strict_types=1);

namespace Loki\AdminComponents\Plugin;

use Magento\Framework\View\Element\Template;
use Yireo\CspUtilities\Util\ReplaceInlineScripts;

class AddCspInlineScripts
{
    private const TRUSTED_TEMPLATE_PATH = 'script/';

    /**
     * @param ReplaceInlineScripts $replaceInlineScripts
     * @param string[] $trustedTemplates
     */
    public function __construct(
        private readonly ReplaceInlineScripts $replaceInlineScripts,
        private readonly array $trustedTemplates = []
    ) {
    }

    public function afterToHtml(Template $block, $html): string
    {
        $html = (string)$html;
        if (false === str_starts_with((string)$block->getNameInLayout(), 'loki')) {
            return $html;
        }

        if ($this->isTrustedTemplate((string)$block->getTemplate())) {
            return $this->replaceInlineScripts->replace($html);
        }

        return $this->replaceInlineScripts->replaceMarked($html);
    }

    private function isTrustedTemplate(string $template): bool
    {
        if (in_array($template, $this->trustedTemplates, true)) {
            return true;
        }

        $templateParts = explode('::', $template, 2);
        if (count($templateParts) !== 2 || !str_starts_with($templateParts[0], 'Loki')) {
            return false;
        }

        return str_starts_with($templateParts[1], self::TRUSTED_TEMPLATE_PATH)
            && !str_contains($templateParts[1], '..');
    }
}
