<?php declare(strict_types=1);

namespace Yireo\LokiAdminComponents\Component\Grid;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Element\AbstractBlock;
use Yireo\LokiAdminComponents\Grid\Action\ActionListing;
use Yireo\LokiAdminComponents\Grid\State;
use Yireo\LokiAdminComponents\ProviderHandler\ProviderHandlerInterface;
use Yireo\LokiAdminComponents\ProviderHandler\ProviderHandlerResolver;
use Yireo\LokiComponents\Component\ComponentRepository;

class GridRepository extends ComponentRepository
{
    public function __construct(
        private State $state,
        private ProviderHandlerResolver $providerHandlerResolver,
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
        $this->state->setSearchableFields((array)$this->getBlock()->getSearchableFields());

        return $this->getProviderHandler()->getItems($this->getProvider(), $this->state);
    }

    private function getProvider()
    {
        $providerClass = $this->getBlock()->getProvider();
        $provider = $this->objectManager->get($providerClass);

        if (!empty($provider)) {
            return $provider;
        }

        throw new \RuntimeException('Empty provider "'.$providerClass.'"');
    }

    private function getBlock(): AbstractBlock
    {
        return $this->getComponent()->getViewModel()->getBlock();
    }

    private function getProviderHandler(): ProviderHandlerInterface
    {
        $providerHandlerName = (string)$this->getBlock()->getProviderHandler();

        return $this->providerHandlerResolver->resolve($providerHandlerName);
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
