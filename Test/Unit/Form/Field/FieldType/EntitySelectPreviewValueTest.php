<?php declare(strict_types=1);

namespace Loki\AdminComponents\Test\Unit\Form\Field\FieldType;

use Loki\AdminComponents\Form\Field\FieldType\CustomerSelect;
use Loki\AdminComponents\Form\Field\FieldType\EntitySelect;
use Loki\AdminComponents\Form\Field\FieldType\EntitySelectInterface;
use Loki\AdminComponents\Form\Field\FieldType\OrderSelect;
use Loki\AdminComponents\Form\Field\FieldType\ProductSelect;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Customer\Model\Data\Customer;
use Magento\Framework\DataObject;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class EntitySelectPreviewValueTest extends TestCase
{
    public static function getFieldTypeClasses(): array
    {
        return [
            'entity_select' => [EntitySelect::class],
            'customer_select' => [CustomerSelect::class],
            'product_select' => [ProductSelect::class],
            'order_select' => [OrderSelect::class],
        ];
    }

    #[DataProvider('getFieldTypeClasses')]
    public function testPreviewValueIsEmptyWithoutSelectedItem(string $fieldTypeClass): void
    {
        $fieldType = $this->createFieldType($fieldTypeClass);
        $this->assertSame('', $fieldType->getPreviewValue(null));
    }

    #[DataProvider('getFieldTypeClasses')]
    public function testPreviewValueIsEmptyWithUnexpectedItem(string $fieldTypeClass): void
    {
        $fieldType = $this->createFieldType($fieldTypeClass);
        $this->assertSame('', $fieldType->getPreviewValue('foobar'));
    }

    public function testEntitySelectPreviewValue(): void
    {
        $item = new DataObject(['name' => 'Foobar']);
        $this->assertSame('Foobar', (new EntitySelect())->getPreviewValue($item));
    }

    public function testCustomerSelectPreviewValue(): void
    {
        $item = $this->createMock(Customer::class);
        $item->method('getFirstname')->willReturn('John');
        $item->method('getLastname')->willReturn('Doe');
        $item->method('getEmail')->willReturn('john@example.com');

        $this->assertSame('John Doe (john@example.com)', (new CustomerSelect())->getPreviewValue($item));
    }

    public function testProductSelectPreviewValue(): void
    {
        $item = $this->createMock(ProductInterface::class);
        $item->method('getName')->willReturn('Foobar');
        $item->method('getSku')->willReturn('FOO-1');

        $this->assertSame('Foobar (FOO-1)', (new ProductSelect())->getPreviewValue($item));
    }

    public function testOrderSelectPreviewValue(): void
    {
        $item = $this->createMock(OrderInterface::class);
        $item->method('getIncrementId')->willReturn('000000042');

        $fieldType = $this->createFieldType(OrderSelect::class);
        $this->assertSame('#000000042', $fieldType->getPreviewValue($item));
    }

    private function createFieldType(string $fieldTypeClass): EntitySelectInterface
    {
        if ($fieldTypeClass === OrderSelect::class) {
            return new OrderSelect($this->createMock(OrderRepositoryInterface::class));
        }

        return new $fieldTypeClass();
    }
}
