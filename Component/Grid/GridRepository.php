<?php declare(strict_types=1);

namespace Yireo\LokiAdminComponents\Component\Grid;

use Yireo\LokiAdminComponents\Grid\GridDataProvider\GridDataProviderInterface;
use Yireo\LokiAdminComponents\Grid\State;
use Yireo\LokiComponents\Component\ComponentRepository;

/**
 * @method GridViewModel getViewModel()
 */
class GridRepository extends ComponentRepository
{
    public function __construct(
        private State $state,
    ) {
    }

    public function getValue(): mixed
    {
        return $this->getDataProvider()->getItems();
    }

    public function getDataProvider(): GridDataProviderInterface
    {
        return $this->getComponent()->getViewModel()->getDataProvider();
    }

    public function saveValue(mixed $value): void
    {
        if (!is_array($value)) {
            return;
        }

        if (isset($value['search'])) {
            $search = $value['search']; // @todo: Add some security here
            $this->state->setSearch($search);
        }

        if (isset($value['page'])) {
            $this->state->setPage((int)$value['page']);
        }

        if (isset($value['limit'])) {
            $this->state->setLimit((int)$value['limit']);
        }
    }
}
