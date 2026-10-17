<?php declare(strict_types=1);

namespace Loki\AdminComponents\Test\Unit\Grid;

use Loki\AdminComponents\Grid\Column\ColumnFactory;
use Loki\AdminComponents\Grid\Grid;
use Loki\AdminComponents\Test\Unit\Traits\CreateButtonFactory;
use PHPUnit\Framework\TestCase;

class GridTest extends TestCase
{
    use CreateButtonFactory;

    public function testAddButtonKeysButtonsById(): void
    {
        $grid = $this->getGrid();
        $factory = $this->createButtonFactory();
        $new = $factory->createNewAction();
        $linkA = $factory->createLinkAction('https://example.com/a', 'A');
        $linkB = $factory->createLinkAction('https://example.com/b', 'B');

        $this->assertSame($grid, $grid->addButton($new));
        $grid->addButton($linkA)->addButton($linkB);

        $this->assertSame(
            ['new' => $new, 'link:https://example.com/a' => $linkA, 'link:https://example.com/b' => $linkB],
            $grid->getButtons()
        );
    }

    private function getGrid(): Grid
    {
        return new Grid(
            $this->createMock(ColumnFactory::class),
            $this->createButtonFactory(),
        );
    }
}
