<?php declare(strict_types=1);

namespace Yireo\LokiAdminComponents\Component\Grid;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Element\AbstractBlock;
use RuntimeException;
use Yireo\LokiAdminComponents\Grid\Action\ActionListing;
use Yireo\LokiAdminComponents\Grid\State;
use Yireo\LokiAdminComponents\ProviderHandler\ProviderHandlerInterface;
use Yireo\LokiAdminComponents\ProviderHandler\ProviderHandlerListing;
use Yireo\LokiComponents\Component\ComponentRepository;

class GridRepository extends ComponentRepository
{
    public function __construct(
        private State $state,
        private ProviderHandlerListing $providerHandlerListing,
        private ObjectManagerInterface $objectManager,
        private ActionListing $actionListing
    ) {
    }

    // @todo: Remove the requirement for this
    public function getValue(): mixed
    {
        return $this->getItems();
    }

    public function getItems(): array
    {
        $searchableFields = $this->getComponent()->getViewModel()->getSearchableFields();
        $this->state->setSearchableFields($searchableFields);

        return $this->getProviderHandler()->getItems($this->getProvider(), $this->state);
    }

    public function getProviderHandler(): ProviderHandlerInterface
    {
        $providerHandlerName = (string)$this->getBlock()->getProviderHandler();
        if (!empty($providerHandlerName)) {
            return $this->providerHandlerListing->getByName($providerHandlerName);
        }

        $provider = $this->getProvider();
        return $this->providerHandlerListing->getByProvider($provider);
    }

    public function getProvider()
    {
        $provider = $this->getBlock()->getProvider();
        if (is_object($provider)) {
            return $provider;
        }

        $provider = $this->objectManager->get($provider);

        if (!empty($provider)) {
            return $provider;
        }

        throw new RuntimeException('Empty grid provider for block "'.$this->getBlock()->getNameInLayout().'"');
    }

    private function getBlock(): AbstractBlock
    {
        return $this->getComponent()->getViewModel()->getBlock();
    }

    public function saveValue(mixed $value): void
    {
        if (!is_array($value)) {
            return;
        }

        foreach ($this->actionListing->getActions() as $action) {
            $action->execute($this, $value);
        }
    }
}
