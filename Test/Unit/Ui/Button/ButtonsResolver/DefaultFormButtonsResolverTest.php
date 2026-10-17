<?php declare(strict_types=1);

namespace Loki\AdminComponents\Test\Unit\Ui\Button\ButtonsResolver;

use Loki\AdminComponents\Component\Form\FormRepository;
use Loki\AdminComponents\Component\Grid\GridRepository;
use Loki\AdminComponents\Test\Unit\Traits\CreateButtonFactory;
use Loki\AdminComponents\Ui\Button\ButtonsResolver\DefaultFormButtonsResolver;
use Magento\Framework\DataObject;
use PHPUnit\Framework\TestCase;

class DefaultFormButtonsResolverTest extends TestCase
{
    use CreateButtonFactory;

    public function testExistingButtonsAreReturnedUnchanged(): void
    {
        $existing = ['send' => $this->createButton('send')];
        $repository = $this->createMock(FormRepository::class);
        $repository->expects($this->never())->method('getItem');

        $this->assertSame($existing, $this->getResolver()->resolve($repository, $existing));
    }

    public function testNonFormRepositoryReturnsNoButtons(): void
    {
        $repository = $this->createMock(GridRepository::class);
        $this->assertSame([], $this->getResolver()->resolve($repository));
    }

    public function testButtonsForNewItem(): void
    {
        $repository = $this->createMock(FormRepository::class);
        $repository->method('getItem')->willReturn(new DataObject());

        $buttons = $this->getResolver()->resolve($repository);
        $this->assertSame(['close', 'saveAndClose'], $this->getButtonIds($buttons));
    }

    public function testButtonsWithoutItem(): void
    {
        $repository = $this->createMock(FormRepository::class);
        $repository->method('getItem')->willReturn(null);

        $buttons = $this->getResolver()->resolve($repository);
        $this->assertSame(['close', 'saveAndClose'], $this->getButtonIds($buttons));
    }

    public function testButtonsForExistingItem(): void
    {
        $repository = $this->createMock(FormRepository::class);
        $repository->method('getItem')->willReturn(new DataObject(['id' => 42]));

        $buttons = $this->getResolver()->resolve($repository);
        $this->assertSame(
            ['close', 'delete', 'saveAndContinue', 'saveAndDuplicate', 'saveAndClose'],
            $this->getButtonIds($buttons)
        );
    }

    private function getButtonIds(array $buttons): array
    {
        return array_values(array_map(fn ($button) => $button->getId(), $buttons));
    }

    private function getResolver(): DefaultFormButtonsResolver
    {
        return new DefaultFormButtonsResolver($this->createButtonFactory());
    }
}
