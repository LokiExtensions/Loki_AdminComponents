<?php
declare(strict_types=1);

namespace Loki\AdminComponents\Grid;

use Loki\AdminComponents\Grid\Column\Column;
use Loki\AdminComponents\Grid\Column\ColumnFactory;
use Loki\AdminComponents\Ui\Button\Button;
use Loki\AdminComponents\Ui\Button\ButtonFactory;

class Grid
{
    /**
     * @var Button[]
     */
    private array $buttons = [];

    /**
     * @var Column[]
     */
    private array $columns = [];

    public function __construct(
        protected ColumnFactory $columnFactory,
        protected ButtonFactory $buttonFactory,
    ) {
    }

    public function addButton(Button $button): Grid
    {
        $this->buttons[] = $button;
        return $this;
    }

    public function getButtons(): array
    {
        return $this->buttons;
    }

    public function addColumn(Column $column): Grid
    {
        $this->columns[] = $column;
        return $this;
    }

    public function getColumns(): array
    {
        return $this->columns;
    }
}
