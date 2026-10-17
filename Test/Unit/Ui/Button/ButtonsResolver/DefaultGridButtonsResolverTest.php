<?php declare(strict_types=1);

namespace Loki\AdminComponents\Test\Unit\Ui\Button\ButtonsResolver;

use Loki\AdminComponents\Component\Form\FormRepository;
use Loki\AdminComponents\Component\Grid\GridRepository;
use Loki\AdminComponents\Test\Unit\Traits\CreateButtonFactory;
use Loki\AdminComponents\Ui\Button\ButtonsResolver\DefaultGridButtonsResolver;
use PHPUnit\Framework\TestCase;

class DefaultGridButtonsResolverTest extends TestCase
{
    use CreateButtonFactory;

    public function testExistingButtonsAreReturnedUnchanged(): void
    {
        $existing = ['new' => $this->createButton('new')];
        $repository = $this->createMock(GridRepository::class);

        $this->assertSame($existing, (new DefaultGridButtonsResolver())->resolve($repository, $existing));
    }

    public function testGridRepositoryHasNoDefaultButtons(): void
    {
        $repository = $this->createMock(GridRepository::class);
        $this->assertSame([], (new DefaultGridButtonsResolver())->resolve($repository));
    }

    public function testNonGridRepositoryReturnsNoButtons(): void
    {
        $repository = $this->createMock(FormRepository::class);
        $this->assertSame([], (new DefaultGridButtonsResolver())->resolve($repository));
    }
}
