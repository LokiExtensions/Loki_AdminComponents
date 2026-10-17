<?php declare(strict_types=1);

namespace Loki\AdminComponents\Test\Unit\Form\Field;

use Loki\AdminComponents\Component\Form\FormRepository;
use Loki\AdminComponents\Form\Field\Field;
use Loki\AdminComponents\Form\Field\FieldFactory;
use Loki\AdminComponents\Form\Field\FieldsResolver;
use Loki\AdminComponents\Form\Field\FieldsResolver\FieldsResolverInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\LayoutInterface;
use PHPUnit\Framework\TestCase;

class FieldsResolverTest extends TestCase
{
    public function testWithoutResolversAndWithoutResourceModelReturnsNoFields(): void
    {
        $repository = $this->createFormRepository();
        $this->assertSame([], $this->getFieldsResolver()->resolve($repository, []));
    }

    public function testResolverFieldsAreReturnedWithoutResourceModel(): void
    {
        $email = $this->createField('email');
        $repository = $this->createFormRepository();

        $fields = $this->getFieldsResolver([$this->createResolver(['email' => $email])])->resolve($repository, []);

        $this->assertSame(['email' => $email], $fields);
    }

    public function testResolversAreChainedInOrder(): void
    {
        $repository = $this->createFormRepository();
        $first = $this->createField('first');
        $second = $this->createField('second');

        $firstResolver = $this->createMock(FieldsResolverInterface::class);
        $firstResolver->expects($this->once())->method('resolve')
            ->with($repository, [])
            ->willReturn(['first' => $first]);

        $secondResolver = $this->createMock(FieldsResolverInterface::class);
        $secondResolver->expects($this->once())->method('resolve')
            ->with($repository, ['first' => $first])
            ->willReturn(['first' => $first, 'second' => $second]);

        $fields = $this->getFieldsResolver([$firstResolver, $secondResolver])->resolve($repository, []);
        $this->assertSame(['first' => $first, 'second' => $second], $fields);
    }

    public function testTableFieldsAreMergedWithResolverFields(): void
    {
        $repository = $this->createFormRepository([
            'id' => ['COLUMN_NAME' => 'id', 'DATA_TYPE' => 'int'],
            'name' => ['COLUMN_NAME' => 'name', 'DATA_TYPE' => 'varchar'],
            'active' => ['COLUMN_NAME' => 'active', 'DATA_TYPE' => 'tinyint'],
        ]);

        $providerName = $this->createField('name', ['label' => 'Provider Name']);
        $email = $this->createField('email');

        $fields = $this->getFieldsResolver([
            $this->createResolver(['name' => $providerName, 'email' => $email]),
        ])->resolve($repository, [
            'active' => ['label' => 'Enabled'],
        ]);

        $this->assertSame(['name', 'email', 'id', 'active'], array_keys($fields));
        $this->assertSame($providerName, $fields['name'], 'Resolver fields take precedence');
        $this->assertSame($email, $fields['email']);
        $this->assertSame('view', $fields['id']->getData('field_type'));
        $this->assertSame('switch', $fields['active']->getData('field_type'));
        $this->assertSame('Enabled', $fields['active']->getLabel());
    }

    public function testFieldsAreSortedBySortOrder(): void
    {
        $repository = $this->createFormRepository([
            'name' => ['COLUMN_NAME' => 'name', 'DATA_TYPE' => 'varchar'],
        ]);

        $late = $this->createField('late', ['sort_order' => 20]);
        $early = $this->createField('early', ['sort_order' => -10]);

        $fields = $this->getFieldsResolver([
            $this->createResolver(['late' => $late, 'early' => $early]),
        ])->resolve($repository, ['name' => ['sort_order' => 10]]);

        $this->assertSame(['early', 'name', 'late'], array_keys($fields));
    }

    private function createField(string $code, array $data = []): Field
    {
        return new Field(null, array_merge(['code' => $code], $data));
    }

    private function createResolver(array $fields): FieldsResolverInterface
    {
        $resolver = $this->createMock(FieldsResolverInterface::class);
        $resolver->method('resolve')->willReturnCallback(
            fn ($repository, array $existingFields) => array_merge($existingFields, $fields)
        );

        return $resolver;
    }

    private function createFormRepository(?array $tableColumns = null): FormRepository
    {
        $repository = $this->createMock(FormRepository::class);
        $repository->method('getPrimaryKey')->willReturn('id');

        if (null === $tableColumns) {
            $repository->method('getResourceModel')->willReturn(null);
            return $repository;
        }

        $connection = $this->createMock(AdapterInterface::class);
        $connection->method('describeTable')->with('example')->willReturn($tableColumns);

        $resourceModel = $this->createMock(AbstractDb::class);
        $resourceModel->method('getConnection')->willReturn($connection);
        $resourceModel->method('getMainTable')->willReturn('example');

        $repository->method('getResourceModel')->willReturn($resourceModel);

        return $repository;
    }

    private function getFieldsResolver(array $fieldsResolvers = []): FieldsResolver
    {
        $fieldFactory = $this->createMock(FieldFactory::class);
        $fieldFactory->method('create')->willReturnCallback(
            fn ($block, array $data) => new Field($block, $data)
        );

        $layout = $this->createMock(LayoutInterface::class);
        $layout->method('createBlock')->willReturn($this->createPartialMock(Template::class, []));

        return new FieldsResolver($fieldFactory, $layout, $fieldsResolvers);
    }
}
