<?php declare(strict_types=1);

namespace Loki\AdminComponents\Test\Integration\Form;

use Loki\AdminComponents\Component\Form\FormRepository;
use Loki\AdminComponents\Form\Field\Field;
use Loki\AdminComponents\Form\Fieldset\Fieldset;
use Loki\AdminComponents\Form\Fieldset\FieldsetsResolver;
use Loki\AdminComponents\Form\Form;
use Loki\AdminComponents\Form\FormBuilder;
use Loki\AdminComponents\Provider\FormProviderInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Fixture\AppArea;
use PHPUnit\Framework\TestCase;

#[AppArea('adminhtml')]
class ProviderFormFieldsetsTest extends TestCase
{
    public function testProviderFieldsetsAndFieldsAreResolved(): void
    {
        $formBuilder = ObjectManager::getInstance()->get(FormBuilder::class);
        $form = $formBuilder->createForm('example_form')
            ->addFieldset(
                $formBuilder->createFieldset('base', 'Generic Settings')
                    ->addField($formBuilder->createField('mail_from', 'Mail From', true))
            )
            ->addFieldset(
                $formBuilder->createFieldset('customer', 'Customer')
                    ->addField($formBuilder->createField('customer_id', 'Customer'))
            )
            ->addField($formBuilder->createField('comment', 'Comment', fieldType: 'textarea'));

        $fieldsets = $this->resolveFieldsets($form, [
            'fields' => [
                'note' => ['label' => 'Note', 'fieldset' => 'customer'],
            ],
        ]);

        $this->assertSame(['base', 'customer'], array_keys($fieldsets));
        $this->assertSame('Generic Settings', $fieldsets['base']->getLabel());
        $this->assertSame(['mail_from', 'comment'], $this->getFieldCodes($fieldsets['base']));
        $this->assertSame('Customer', $fieldsets['customer']->getLabel());
        $this->assertSame(['customer_id', 'note'], $this->getFieldCodes($fieldsets['customer']));
    }

    public function testNoEmptyBaseFieldsetIsAddedForProviderWithoutBaseFieldset(): void
    {
        $formBuilder = ObjectManager::getInstance()->get(FormBuilder::class);
        $form = $formBuilder->createForm('example_form')
            ->addFieldset(
                $formBuilder->createFieldset('general', 'General')
                    ->addField($formBuilder->createField('name', 'Name'))
            );

        $fieldsets = $this->resolveFieldsets($form);

        $this->assertSame(['general'], array_keys($fieldsets));
    }

    private function resolveFieldsets(Form $form, array $blockData = []): array
    {
        $provider = $this->createMock(FormProviderInterface::class);
        $provider->method('getForm')->willReturn($form);

        $repository = $this->createMock(FormRepository::class);
        $repository->method('getProvider')->willReturn($provider);
        $repository->method('getResourceModel')->willReturn(null);

        $block = ObjectManager::getInstance()->get(LayoutInterface::class)
            ->createBlock(Template::class, 'loki-admin-components.test.form.'.uniqid(), ['data' => $blockData]);

        return ObjectManager::getInstance()->create(FieldsetsResolver::class)->resolve($repository, $block);
    }

    private function getFieldCodes(Fieldset $fieldset): array
    {
        return array_values(array_map(fn (Field $field) => $field->getCode(), $fieldset->getFields()));
    }
}
