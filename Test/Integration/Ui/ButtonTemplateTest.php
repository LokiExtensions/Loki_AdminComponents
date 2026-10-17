<?php declare(strict_types=1);

namespace Loki\AdminComponents\Test\Integration\Ui;

use Loki\AdminComponents\Ui\Button\ButtonFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Fixture\AppArea;
use PHPUnit\Framework\TestCase;

#[AppArea('adminhtml')]
class ButtonTemplateTest extends TestCase
{
    public function testSimpleButton(): void
    {
        $buttonFactory = ObjectManager::getInstance()->get(ButtonFactory::class);
        $button = $buttonFactory->createLinkAction('https://example.com/new?a=1&b=2', 'Add New', 'add', primary: true);

        $html = $this->render($button);

        $this->assertStringContainsString('@click.prevent="redirectAction"', $html);
        $this->assertStringContainsString('data-url="https://example.com/new?a=1&amp;b=2"', $html);
        $this->assertStringContainsString('class="scalable add primary"', $html);
        $this->assertStringContainsString('<span>Add New</span>', $html);
        $this->assertStringNotContainsString('class="actions-split', $html);
    }

    public function testButtonWithoutUrlHasNoDataUrl(): void
    {
        $buttonFactory = ObjectManager::getInstance()->get(ButtonFactory::class);
        $html = $this->render($buttonFactory->createNewAction());

        $this->assertStringContainsString('@click.prevent="newAction"', $html);
        $this->assertStringNotContainsString('data-url', $html);
    }

    public function testSubButtonsUseTheirOwnUrl(): void
    {
        $buttonFactory = ObjectManager::getInstance()->get(ButtonFactory::class);
        $subButtons = [
            $buttonFactory->createLinkAction('https://example.com/virtual', 'Virtual Product'),
            $buttonFactory->createLinkAction('https://example.com/bundle', 'Bundle Product'),
            $buttonFactory->create('custom', 'customAction', 'Custom Action'),
        ];

        $button = $buttonFactory->createLinkAction(
            'https://example.com/simple',
            'Add Product',
            subButtons: $subButtons,
            primary: true
        );

        $html = $this->render($button);

        $this->assertStringContainsString('class="actions-split primary"', $html);
        $this->assertSame(1, substr_count($html, 'data-url="https://example.com/simple"'));
        $this->assertSame(1, substr_count($html, 'data-url="https://example.com/virtual"'));
        $this->assertSame(1, substr_count($html, 'data-url="https://example.com/bundle"'));
        $this->assertSame(3, substr_count($html, 'data-url='));
        $this->assertStringContainsString('@click.prevent="customAction"', $html);
        $this->assertStringContainsString('Virtual Product', $html);
        $this->assertStringContainsString('Bundle Product', $html);
        $this->assertStringContainsString('Custom Action', $html);
    }

    public function testAttributesAreEscaped(): void
    {
        $buttonFactory = ObjectManager::getInstance()->get(ButtonFactory::class);
        $button = $buttonFactory->create('evil', 'evil"Action', 'Evil <b>label</b>', 'evil" onclick="alert(1)');

        $html = $this->render($button);

        $this->assertStringNotContainsString('onclick="alert(1)"', $html);
        $this->assertStringNotContainsString('<b>label</b>', $html);
        $this->assertStringNotContainsString('evil"Action', $html);
    }

    private function render(object $button): string
    {
        $layout = ObjectManager::getInstance()->get(LayoutInterface::class);
        $block = $layout->createBlock(
            Template::class,
            'loki-admin-components.test.button.'.uniqid(),
            ['data' => ['template' => 'Loki_AdminComponents::ui/button.phtml', 'button' => $button]]
        );
        $block->assign('css', fn (string $cssClass = '', string $default = '') => $cssClass);

        return $block->toHtml();
    }
}
