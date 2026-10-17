<?php declare(strict_types=1);

namespace Loki\AdminComponents\Test\Unit\Ui\Button;

use Loki\AdminComponents\Component\Form\FormRepository;
use Loki\AdminComponents\Test\Unit\Traits\CreateButtonFactory;
use Loki\AdminComponents\Ui\Button\ButtonsResolver;
use Loki\AdminComponents\Ui\Button\ButtonsResolver\ButtonsResolverInterface;
use PHPUnit\Framework\TestCase;

class ButtonsResolverTest extends TestCase
{
    use CreateButtonFactory;

    public function testWithoutResolversReturnsNoButtons(): void
    {
        $repository = $this->createMock(FormRepository::class);
        $this->assertSame([], (new ButtonsResolver())->resolve($repository));
    }

    public function testResolversAreChainedInOrder(): void
    {
        $repository = $this->createMock(FormRepository::class);
        $first = $this->createButton('first');
        $second = $this->createButton('second');

        $firstResolver = $this->createMock(ButtonsResolverInterface::class);
        $firstResolver->expects($this->once())
            ->method('resolve')
            ->with($repository, [])
            ->willReturn(['first' => $first]);

        $secondResolver = $this->createMock(ButtonsResolverInterface::class);
        $secondResolver->expects($this->once())
            ->method('resolve')
            ->with($repository, ['first' => $first])
            ->willReturn(['first' => $first, 'second' => $second]);

        $buttonsResolver = new ButtonsResolver([
            'first' => $firstResolver,
            'second' => $secondResolver,
        ]);

        $this->assertSame(['first' => $first, 'second' => $second], $buttonsResolver->resolve($repository));
    }
}
