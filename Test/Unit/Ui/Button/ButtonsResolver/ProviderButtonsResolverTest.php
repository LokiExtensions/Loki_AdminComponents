<?php declare(strict_types=1);

namespace Loki\AdminComponents\Test\Unit\Ui\Button\ButtonsResolver;

use Loki\AdminComponents\Component\Form\FormRepository;
use Loki\AdminComponents\Component\Grid\GridRepository;
use Loki\AdminComponents\Form\Form;
use Loki\AdminComponents\Grid\Grid;
use Loki\AdminComponents\Provider\ButtonsProviderInterface;
use Loki\AdminComponents\Provider\FormProviderInterface;
use Loki\AdminComponents\Provider\GridProviderInterface;
use Loki\AdminComponents\Test\Unit\Traits\CreateButtonFactory;
use Loki\AdminComponents\Test\Unit\Traits\CreateRepository;
use Loki\AdminComponents\Ui\Button\ButtonsResolver\ProviderButtonsResolver;
use Loki\Components\Component\ComponentRepository;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;

class ProviderButtonsResolverTest extends TestCase
{
    use CreateButtonFactory;
    use CreateRepository;

    public function testWithOtherRepositoryReturnsButtonsUnchanged(): void
    {
        $existing = ['close' => $this->createButton('close')];
        $repository = $this->createMock(ComponentRepository::class);

        $this->assertSame($existing, (new ProviderButtonsResolver())->resolve($repository, $existing));
    }

    #[DataProvider('getRepositoryClasses')]
    public function testWithoutProviderReturnsButtonsUnchanged(string $repositoryClass): void
    {
        $existing = ['close' => $this->createButton('close')];
        $repository = $this->createRepository($repositoryClass);

        $this->assertSame($existing, (new ProviderButtonsResolver())->resolve($repository, $existing));
        $this->assertSame([], (new ProviderButtonsResolver())->resolve($repository));
    }

    #[DataProvider('getRepositoryClasses')]
    public function testWithGenericProviderReturnsButtonsUnchanged(string $repositoryClass): void
    {
        $existing = ['close' => $this->createButton('close')];
        $repository = $this->createRepository($repositoryClass, provider: new stdClass());

        $this->assertSame($existing, (new ProviderButtonsResolver())->resolve($repository, $existing));
    }

    public static function getRepositoryClasses(): array
    {
        return [
            [FormRepository::class],
            [GridRepository::class],
        ];
    }

    public function testFormProviderButtonsAreAdded(): void
    {
        $send = $this->createButton('send');
        $preview = $this->createButton('preview');
        $form = $this->createMock(Form::class);
        $form->method('getButtons')->willReturn(['send' => $send, 'preview' => $preview]);

        $provider = $this->createMock(FormProviderInterface::class);
        $provider->method('getForm')->willReturn($form);

        $repository = $this->createRepository(FormRepository::class, provider: $provider);
        $existing = ['close' => $this->createButton('close')];

        $this->assertSame(
            ['close' => $existing['close'], 'send' => $send, 'preview' => $preview],
            (new ProviderButtonsResolver())->resolve($repository, $existing)
        );
    }

    public function testFormProviderIsIgnoredForGridRepository(): void
    {
        $provider = $this->createMock(FormProviderInterface::class);
        $provider->expects($this->never())->method('getForm');

        $repository = $this->createRepository(GridRepository::class, provider: $provider);
        $this->assertSame([], (new ProviderButtonsResolver())->resolve($repository));
    }

    public function testGridProviderButtonsAreAdded(): void
    {
        $new = $this->createButton('new');
        $grid = $this->createMock(Grid::class);
        $grid->method('getButtons')->willReturn(['new' => $new]);

        $provider = $this->createMock(GridProviderInterface::class);
        $provider->method('getGrid')->willReturn($grid);

        $repository = $this->createRepository(GridRepository::class, provider: $provider);

        $this->assertSame(['new' => $new], (new ProviderButtonsResolver())->resolve($repository));
    }

    public function testGridProviderIsIgnoredForFormRepository(): void
    {
        $provider = $this->createMock(GridProviderInterface::class);
        $provider->expects($this->never())->method('getGrid');

        $repository = $this->createRepository(FormRepository::class, provider: $provider);
        $this->assertSame([], (new ProviderButtonsResolver())->resolve($repository));
    }

    #[DataProvider('getRepositoryClasses')]
    public function testButtonsProviderButtonsAreAdded(string $repositoryClass): void
    {
        $custom = $this->createButton('custom');
        $provider = $this->createMock(ButtonsProviderInterface::class);
        $provider->method('getButtons')->willReturn(['custom' => $custom]);

        $repository = $this->createRepository($repositoryClass, provider: $provider);

        $this->assertSame(['custom' => $custom], (new ProviderButtonsResolver())->resolve($repository));
    }

    public function testButtonsProviderOverridesFormProviderButtonsWithSameId(): void
    {
        $formSend = $this->createButton('send', 'sendAction', 'Send');
        $providerSend = $this->createButton('send', 'sendAction', 'Send now');

        $form = $this->createMock(Form::class);
        $form->method('getButtons')->willReturn(['send' => $formSend]);

        $provider = $this->createMockForIntersectionOfInterfaces([
            FormProviderInterface::class,
            ButtonsProviderInterface::class,
        ]);
        $provider->method('getForm')->willReturn($form);
        $provider->method('getButtons')->willReturn(['send' => $providerSend]);

        $repository = $this->createRepository(FormRepository::class, provider: $provider);

        $this->assertSame(['send' => $providerSend], (new ProviderButtonsResolver())->resolve($repository));
    }
}
