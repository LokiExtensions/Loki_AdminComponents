<?php declare(strict_types=1);

namespace Loki\AdminComponents\Test\Unit\Form\Field\FieldsResolver;

use Loki\AdminComponents\Component\Form\FormRepository;
use Loki\AdminComponents\Component\Grid\GridRepository;
use Loki\AdminComponents\Form\Field\Field;
use Loki\AdminComponents\Form\Field\FieldsResolver\BlockFieldsResolver;
use Loki\AdminComponents\Form\Field\FieldsResolver\FieldsResolverInterface;
use Loki\AdminComponents\Form\Field\FieldsResolver\ProviderFieldsResolver;
use Loki\AdminComponents\Form\Form;
use Loki\AdminComponents\Provider\FormProviderInterface;
use Loki\AdminComponents\Test\Unit\Traits\CreateRepository;
use Loki\Components\Component\ComponentRepository;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;

class ProviderFieldsResolverTest extends TestCase
{
    use CreateRepository;

    #[DataProvider('getResolverClasses')]
    public function testWithOtherRepositoryReturnsFieldsUnchanged(string $resolverClass): void
    {
        $existing = ['name' => $this->createMock(Field::class)];
        $repository = $this->createMock(ComponentRepository::class);

        $this->assertSame($existing, $this->getResolver($resolverClass)->resolve($repository, $existing));
    }

    #[DataProvider('getResolverClasses')]
    public function testWithoutProviderReturnsFieldsUnchanged(string $resolverClass): void
    {
        $existing = ['name' => $this->createMock(Field::class)];
        $repository = $this->createRepository(FormRepository::class);

        $this->assertSame($existing, $this->getResolver($resolverClass)->resolve($repository, $existing));
        $this->assertSame([], $this->getResolver($resolverClass)->resolve($repository));
    }

    #[DataProvider('getResolverClasses')]
    public function testWithGenericProviderReturnsFieldsUnchanged(string $resolverClass): void
    {
        $repository = $this->createRepository(FormRepository::class, provider: new stdClass());
        $this->assertSame([], $this->getResolver($resolverClass)->resolve($repository));
    }

    #[DataProvider('getResolverClasses')]
    public function testFormProviderFieldsAreAdded(string $resolverClass): void
    {
        $existing = $this->createMock(Field::class);
        $email = $this->createMock(Field::class);
        $form = $this->createMock(Form::class);
        $form->method('getFields')->willReturn(['email' => $email]);

        $provider = $this->createMock(FormProviderInterface::class);
        $provider->method('getForm')->willReturn($form);

        $repository = $this->createRepository(FormRepository::class, provider: $provider);

        $this->assertSame(
            ['name' => $existing, 'email' => $email],
            $this->getResolver($resolverClass)->resolve($repository, ['name' => $existing])
        );
    }

    #[DataProvider('getResolverClasses')]
    public function testFormProviderIsIgnoredForGridRepository(string $resolverClass): void
    {
        $provider = $this->createMock(FormProviderInterface::class);
        $provider->expects($this->never())->method('getForm');

        $repository = $this->createRepository(GridRepository::class, provider: $provider);
        $this->assertSame([], $this->getResolver($resolverClass)->resolve($repository));
    }

    public static function getResolverClasses(): array
    {
        return [
            [ProviderFieldsResolver::class],
            [BlockFieldsResolver::class],
        ];
    }

    private function getResolver(string $resolverClass): FieldsResolverInterface
    {
        return new $resolverClass();
    }
}
