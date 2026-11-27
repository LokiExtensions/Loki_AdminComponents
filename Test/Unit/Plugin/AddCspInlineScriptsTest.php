<?php declare(strict_types=1);

namespace Loki\AdminComponents\Test\Unit\Plugin;

use Loki\AdminComponents\Plugin\AddCspInlineScripts;
use Magento\Framework\View\Element\Template;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Yireo\CspUtilities\Util\ReplaceInlineScripts;

class AddCspInlineScriptsTest extends TestCase
{
    public function testNonLokiBlocksAreIgnored(): void
    {
        $replaceInlineScripts = $this->createMock(ReplaceInlineScripts::class);
        $replaceInlineScripts->expects($this->never())->method('replace');
        $replaceInlineScripts->expects($this->never())->method('replaceMarked');

        $plugin = new AddCspInlineScripts($replaceInlineScripts);
        $html = '<script>alert(1)</script>';

        $this->assertSame($html, $plugin->afterToHtml($this->getBlock('foobar', 'Loki_AdminComponents::grid.phtml'), $html));
    }

    #[DataProvider('getTrustedTemplates')]
    public function testScriptTemplatesAreTrusted(string $template): void
    {
        $replaceInlineScripts = $this->createMock(ReplaceInlineScripts::class);
        $replaceInlineScripts->expects($this->once())->method('replace')->willReturn('replaced');
        $replaceInlineScripts->expects($this->never())->method('replaceMarked');

        $plugin = new AddCspInlineScripts($replaceInlineScripts, ['Loki_Components::form_key_loader.phtml']);

        $this->assertSame('replaced', $plugin->afterToHtml($this->getBlock('loki.script.foo', $template), '<script></script>'));
    }

    public static function getTrustedTemplates(): array
    {
        return [
            ['Loki_AdminComponents::script/component/grid-component.phtml'],
            ['Loki_Components::script/ajax-queue.phtml'],
            ['Loki_Components::form_key_loader.phtml'],
        ];
    }

    #[DataProvider('getUntrustedTemplates')]
    public function testOtherTemplatesOnlyTrustMarkedScripts(string $template): void
    {
        $replaceInlineScripts = $this->createMock(ReplaceInlineScripts::class);
        $replaceInlineScripts->expects($this->never())->method('replace');
        $replaceInlineScripts->expects($this->once())->method('replaceMarked')->willReturn('marked');

        $plugin = new AddCspInlineScripts($replaceInlineScripts);

        $this->assertSame('marked', $plugin->afterToHtml($this->getBlock('loki-admin.grid.table', $template), '<script></script>'));
    }

    public static function getUntrustedTemplates(): array
    {
        return [
            ['Loki_AdminComponents::grid/table.phtml'],
            ['Loki_AdminComponents::grid/cell/html.phtml'],
            ['Vendor_Module::script/foobar.phtml'],
            ['Loki_AdminComponents::script/../grid/cell/html.phtml'],
            [''],
        ];
    }

    private function getBlock(string $name, string $template): Template
    {
        $block = $this->createPartialMock(Template::class, ['getNameInLayout', 'getTemplate']);
        $block->method('getNameInLayout')->willReturn($name);
        $block->method('getTemplate')->willReturn($template);

        return $block;
    }
}
