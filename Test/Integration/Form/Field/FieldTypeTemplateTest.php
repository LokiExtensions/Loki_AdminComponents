<?php declare(strict_types=1);

namespace Loki\AdminComponents\Test\Integration\Form\Field;

use Loki\AdminComponents\Form\Field\Field;
use Loki\AdminComponents\Form\Field\FieldFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Fixture\AppArea;
use PHPUnit\Framework\TestCase;

#[AppArea('adminhtml')]
class FieldTypeTemplateTest extends TestCase
{
    /**
     * @dataProvider getFieldTypes
     */
    public function testFieldElementHasTheIdOfItsLabel(string $fieldType, string $tagName): void
    {
        $field = $this->createField($fieldType);
        $html = $this->render($field, $fieldType);

        $domId = $field->getDomId();
        $this->assertStringEndsWith('example-code', $domId);
        $this->assertMatchesRegularExpression(
            '/<' . $tagName . '\s[^>]*id="' . preg_quote($domId, '/') . '"/',
            $html,
            'The label of the field points to this ID'
        );
    }

    public function testMultiselectAllowsMultipleOptions(): void
    {
        $multiselectHtml = $this->render($this->createField('multiselect'), 'select');
        $selectHtml = $this->render($this->createField('select'), 'select');

        $this->assertMatchesRegularExpression('/<select\s[^>]*\smultiple[\s>]/', $multiselectHtml);
        $this->assertDoesNotMatchRegularExpression('/<select\s[^>]*\smultiple[\s>]/', $selectHtml);
    }

    public static function getFieldTypes(): array
    {
        return [
            'input' => ['input', 'input'],
            'textarea' => ['textarea', 'textarea'],
            'select' => ['select', 'select'],
        ];
    }

    private function createField(string $fieldType): Field
    {
        return ObjectManager::getInstance()->get(FieldFactory::class)->createWithBlock([
            'code' => 'example_code',
            'label' => 'Example',
            'field_type' => $fieldType,
        ]);
    }

    private function render(Field $field, string $template): string
    {
        $layout = ObjectManager::getInstance()->get(LayoutInterface::class);
        $block = $layout->createBlock(
            Template::class,
            'loki-admin-components.test.field.' . uniqid(),
            ['data' => [
                'template' => 'Loki_AdminComponents::form/field_type/' . $template . '.phtml',
                'field' => $field,
            ]]
        );
        $block->assign('css', fn (string $cssClass = '', string $default = '') => $cssClass);

        return $block->toHtml();
    }
}
