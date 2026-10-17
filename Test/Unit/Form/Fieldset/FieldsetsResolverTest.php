<?php declare(strict_types=1);

namespace Loki\AdminComponents\Test\Unit\Form\Fieldset;

use Loki\AdminComponents\Component\Form\FormRepository;
use Loki\AdminComponents\Form\Field\Field;
use Loki\AdminComponents\Form\Field\FieldFactory;
use Loki\AdminComponents\Form\Field\FieldsResolver;
use Loki\AdminComponents\Form\Fieldset\Fieldset;
use Loki\AdminComponents\Form\Fieldset\FieldsetFactory;
use Loki\AdminComponents\Form\Fieldset\FieldsetsResolver;
use Loki\AdminComponents\Form\Fieldset\FieldsetsResolver\FieldsetsResolverInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Element\Template;
use PHPUnit\Framework\TestCase;

class FieldsetsResolverTest extends TestCase
{
    public function testProviderBaseFieldsetIsNotReplaced(): void
    {
        $mailFrom = $this->createField('mail_from');
        $providerBase = new Fieldset('base', 'Generic Settings', [$mailFrom]);

        $fieldsets = $this->getFieldsetsResolver(
            fieldsetsResolvers: [$this->createFieldsetsResolver(['base' => $providerBase])],
        )->resolve($this->createMock(FormRepository::class), $this->createBlock());

        $this->assertSame(['base' => $providerBase], $fieldsets);
        $this->assertSame('Generic Settings', $fieldsets['base']->getLabel());
        $this->assertSame([$mailFrom], $fieldsets['base']->getFields());
    }

    public function testEmptyFieldsetsAreRemoved(): void
    {
        $customer = new Fieldset('customer', 'Customer', [$this->createField('customer_id')]);

        $fieldsets = $this->getFieldsetsResolver(
            fieldsetsResolvers: [$this->createFieldsetsResolver(['customer' => $customer])],
        )->resolve($this->createMock(FormRepository::class), $this->createBlock([
            'fieldsets' => ['empty' => ['label' => 'Empty']],
        ]));

        $this->assertSame(['customer' => $customer], $fieldsets);
    }

    public function testResolvedFieldsArePlacedInTheirFieldset(): void
    {
        $name = $this->createField('name');
        $email = $this->createField('email', ['fieldset' => 'contact']);
        $unknown = $this->createField('unknown', ['fieldset' => 'unknown']);

        $fieldsets = $this->getFieldsetsResolver(
            resolvedFields: ['name' => $name, 'email' => $email, 'unknown' => $unknown],
        )->resolve($this->createMock(FormRepository::class), $this->createBlock([
            'fieldsets' => ['contact' => ['label' => 'Contact']],
        ]));

        $this->assertSame(['base', 'contact'], array_keys($fieldsets));
        $this->assertSame('Base', $fieldsets['base']->getLabel());
        $this->assertSame([$name, $unknown], $fieldsets['base']->getFields());
        $this->assertSame('Contact', $fieldsets['contact']->getLabel());
        $this->assertSame([$email], $fieldsets['contact']->getFields());
    }

    public function testResolvedFieldsAreNotDuplicatedByBlockDefinitions(): void
    {
        $name = $this->createField('name');

        $fieldsets = $this->getFieldsetsResolver(
            resolvedFields: ['name' => $name],
        )->resolve($this->createMock(FormRepository::class), $this->createBlock([
            'fields' => ['name' => ['label' => 'Name']],
        ]));

        $this->assertSame([$name], $fieldsets['base']->getFields());
    }

    public function testBlockFieldDefinitionsArePlacedInTheirOwnFieldset(): void
    {
        $fieldsets = $this->getFieldsetsResolver()->resolve(
            $this->createMock(FormRepository::class),
            $this->createBlock([
                'fieldsets' => [
                    'base' => ['label' => 'General'],
                    'extra' => ['label' => 'Extra'],
                ],
                'fields' => [
                    'first' => ['label' => 'First', 'fieldset' => 'extra'],
                    'second' => ['label' => 'Second'],
                    'third' => ['label' => 'Third', 'fieldset' => 'unknown'],
                ],
            ])
        );

        $this->assertSame(['base', 'extra'], array_keys($fieldsets));
        $this->assertSame('General', $fieldsets['base']->getLabel());
        $this->assertSame(['second', 'third'], $this->getFieldCodes($fieldsets['base']));
        $this->assertSame(['first'], $this->getFieldCodes($fieldsets['extra']));
    }

    public function testBlockFieldsetWithoutLabelUsesCode(): void
    {
        $fieldsets = $this->getFieldsetsResolver()->resolve(
            $this->createMock(FormRepository::class),
            $this->createBlock([
                'fieldsets' => ['extra' => []],
                'fields' => ['first' => ['fieldset' => 'extra']],
            ])
        );

        $this->assertSame('extra', $fieldsets['extra']->getLabel());
    }

    private function getFieldCodes(Fieldset $fieldset): array
    {
        return array_map(fn (Field $field) => $field->getCode(), $fieldset->getFields());
    }

    private function createField(string $code, array $data = []): Field
    {
        return new Field(null, array_merge(['code' => $code], $data));
    }

    private function createBlock(array $data = []): Template
    {
        $block = $this->createPartialMock(Template::class, []);
        $block->setData($data);

        return $block;
    }

    private function createFieldsetsResolver(array $fieldsets): FieldsetsResolverInterface
    {
        $resolver = $this->createMock(FieldsetsResolverInterface::class);
        $resolver->method('resolve')->willReturnCallback(
            fn ($repository, array $existingFieldsets) => array_merge($existingFieldsets, $fieldsets)
        );

        return $resolver;
    }

    private function getFieldsetsResolver(
        array $resolvedFields = [],
        array $fieldsetsResolvers = [],
    ): FieldsetsResolver {
        $objectManager = $this->createMock(ObjectManagerInterface::class);
        $objectManager->method('create')->willReturnCallback(
            fn (string $className, array $arguments) => new $className(...$arguments)
        );

        $fieldsResolver = $this->createMock(FieldsResolver::class);
        $fieldsResolver->method('resolve')->willReturn($resolvedFields);

        $fieldFactory = $this->createMock(FieldFactory::class);
        $fieldFactory->method('create')->willReturnCallback(
            fn ($block, array $data) => new Field($block, $data)
        );

        return new FieldsetsResolver(
            new FieldsetFactory($objectManager),
            $fieldsResolver,
            $fieldFactory,
            $fieldsetsResolvers
        );
    }
}
