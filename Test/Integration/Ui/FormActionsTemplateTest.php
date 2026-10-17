<?php declare(strict_types=1);

namespace Loki\AdminComponents\Test\Integration\Ui;

use Loki\AdminComponents\Component\Form\FormViewModel;
use Loki\AdminComponents\Ui\Button\ButtonFactory;
use Loki\Components\Util\Block\TemplateRenderer;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Fixture\AppArea;
use PHPUnit\Framework\TestCase;

#[AppArea('adminhtml')]
class FormActionsTemplateTest extends TestCase
{
    public function testFormButtonsAreRenderedWithUrlsAndSubButtons(): void
    {
        $buttonFactory = ObjectManager::getInstance()->get(ButtonFactory::class);
        $buttons = [
            'close' => $buttonFactory->createCloseAction(),
            'docs' => $buttonFactory->createLinkAction('https://example.com/docs', 'Documentation'),
            'saveAndClose' => $buttonFactory->create(
                'saveAndClose',
                'saveAndCloseAction',
                'Save & Close',
                'save',
                subButtons: [
                    $buttonFactory->createSaveContinueAction(),
                    $buttonFactory->createSaveDuplicateAction(),
                ],
                primary: true
            ),
        ];

        $html = $this->render($buttons);

        $this->assertStringContainsString('@click.prevent="closeAction"', $html);
        $this->assertStringContainsString('@click.prevent="redirectAction"', $html);
        $this->assertStringContainsString('data-url="https://example.com/docs"', $html);
        $this->assertStringContainsString('class="actions-split save primary"', $html);
        $this->assertStringContainsString('@click.prevent="saveAndCloseAction"', $html);
        $this->assertStringContainsString('@click.prevent="saveAndContinueAction"', $html);
        $this->assertStringContainsString('@click.prevent="saveAndDuplicateAction"', $html);
        $this->assertStringContainsString('@click.prevent="toggleOtherActions"', $html);
        $this->assertSame(1, substr_count($html, 'saveAndDuplicateAction'));
    }

    public function testNoHardcodedSaveActionsAreRendered(): void
    {
        $buttonFactory = ObjectManager::getInstance()->get(ButtonFactory::class);
        $html = $this->render(['close' => $buttonFactory->createCloseAction()], [
            'other_save_actions' => ['foo' => 'bar'],
        ]);

        $this->assertStringNotContainsString('saveAndDuplicateAction', $html);
        $this->assertStringNotContainsString('saveAndContinueAction', $html);
    }

    public function testFormComponentTypeSupportsRedirectAction(): void
    {
        $layout = ObjectManager::getInstance()->get(LayoutInterface::class);
        $block = $layout->createBlock(
            Template::class,
            'loki-admin-components.test.form-component-type.'.uniqid(),
            ['data' => ['template' => 'Loki_AdminComponents::script/component-type/form-component-type.phtml']]
        );

        $html = $block->toHtml();
        $this->assertStringContainsString('redirectAction() {', $html);
        $this->assertStringContainsString("this.\$el.getAttribute('data-url')", $html);
    }

    private function render(array $buttons, array $blockData = []): string
    {
        $viewModel = $this->createMock(FormViewModel::class);
        $viewModel->method('getButtons')->willReturn($buttons);

        $layout = ObjectManager::getInstance()->get(LayoutInterface::class);
        $block = $layout->createBlock(
            Template::class,
            'loki-admin-components.test.form-actions.'.uniqid(),
            ['data' => array_merge($blockData, [
                'template' => 'Loki_AdminComponents::form/actions.phtml',
                'view_model' => $viewModel,
            ])]
        );

        $block->assign('templateRenderer', ObjectManager::getInstance()->get(TemplateRenderer::class));
        $block->assign('css', fn (string $cssClass = '', string $default = '') => $cssClass);

        return $block->toHtml();
    }
}
