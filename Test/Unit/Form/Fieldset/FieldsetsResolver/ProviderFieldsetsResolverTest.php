<?php declare(strict_types=1);

namespace Loki\AdminComponents\Test\Unit\Form\Fieldset\FieldsetsResolver;

use Loki\AdminComponents\Component\Form\FormRepository;
use Loki\AdminComponents\Component\Grid\GridRepository;
use Loki\AdminComponents\Form\Fieldset\Fieldset;
use Loki\AdminComponents\Form\Fieldset\FieldsetsResolver\BlockFieldsetsResolver;
use Loki\AdminComponents\Form\Fieldset\FieldsetsResolver\FieldsetsResolverInterface;
use Loki\AdminComponents\Form\Fieldset\FieldsetsResolver\ProviderFieldsetsResolver;
use Loki\AdminComponents\Form\Form;
use Loki\AdminComponents\Provider\FormProviderInterface;
use Loki\AdminComponents\Test\Unit\Traits\CreateRepository;
use Loki\Components\Component\ComponentRepository;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;

class ProviderFieldsetsResolverTest extends TestCase
{
    use CreateRepository;

    #[DataProvider('getResolverClasses')]
    public function testWithOtherRepositoryReturnsFieldsetsUnchanged(string $resolverClass): void
    {
        $existing = ['base' => $this->createMock(Fieldset::class)];
        $repository = $this->createMock(ComponentRepository::class);

        $this->assertSame($existing, $this->getResolver($resolverClass)->resolve($repository, $existing));
    }

    #[DataProvider('getResolverClasses')]
    public function testWithoutProviderReturnsFieldsetsUnchanged(string $resolverClass): void
    {
        $existing = ['base' => $this->createMock(Fieldset::class)];
        $repository = $this->createRepository(FormRepository::class);

        $this->assertSame($existing, $this->getResolver($resolverClass)->resolve($repository, $existing));
        $this->assertSame([], $this->getResolver($resolverClass)->resolve($repository));
    }

    #[DataProvider('getResolverClasses')]
    public function testWithGenericProviderReturnsFieldsetsUnchanged(string $resolverClass): void
    {
        $repository = $this->createRepository(FormRepository::class, provider: new stdClass());
        $this->assertSame([], $this->getResolver($resolverClass)->resolve($repository));
    }

    #[DataProvider('getResolverClasses')]
    public function testFormProviderFieldsetsAreAdded(string $resolverClass): void
    {
        $existing = $this->createMock(Fieldset::class);
        $general = $this->createMock(Fieldset::class);
        $form = $this->createMock(Form::class);
        $form->method('getFieldsets')->willReturn(['general' => $general]);

        $provider = $this->createMock(FormProviderInterface::class);
        $provider->method('getForm')->willReturn($form);

        $repository = $this->createRepository(FormRepository::class, provider: $provider);

        $this->assertSame(
            ['base' => $existing, 'general' => $general],
            $this->getResolver($resolverClass)->resolve($repository, ['base' => $existing])
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
            [ProviderFieldsetsResolver::class],
            [BlockFieldsetsResolver::class],
        ];
    }

    private function getResolver(string $resolverClass): FieldsetsResolverInterface
    {
        return new $resolverClass();
    }
}
