<?php declare(strict_types=1);

namespace Loki\AdminComponents\Component\Grid;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Element\AbstractBlock;
use RuntimeException;
use Loki\AdminComponents\Grid\Action\ActionListing;
use Loki\AdminComponents\Grid\State;
use Loki\AdminComponents\Grid\StateManager;
use Loki\AdminComponents\ProviderHandler\ProviderHandlerInterface;
use Loki\AdminComponents\ProviderHandler\ProviderHandlerListing;
use Loki\Components\Component\ComponentRepository;

class GridRepository extends ComponentRepository
{
    public function __construct(
        private StateManager $stateManager,
        private ProviderHandlerListing $providerHandlerListing,
        private ObjectManagerInterface $objectManager,
        private ActionListing $actionListing
    ) {
    }

    public function getNamespace(): string
    {
        return $this->getComponent()->getViewModel()->getNamespace();
    }

    // @todo: Remove the requirement for this
    public function getValue(): mixed
    {
        return $this->getItems();
    }

    public function getPrimaryKey(): string
    {
        if ($this->getResourceModel()) {
            return $this->getResourceModel()->getIdFieldName();
        }

        // @todo: Make this configurable

        return 'id';
    }

    public function getItems(): array
    {
        $searchableFields = $this->getComponent()->getViewModel()->getSearchableFields();
        $this->getState()->setSearchableFields($searchableFields);

        return $this->getProviderHandler()->getItems($this->getProvider(), $this->getState());
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

        if (empty($provider)) {
            throw new RuntimeException('No provider for block "'.$this->getBlock()->getNameInLayout().'"');
        }

        $provider = $this->objectManager->get($provider);

        if (!empty($provider)) {
            return $provider;
        }

        throw new RuntimeException('Empty grid provider for block "'.$this->getBlock()->getNameInLayout().'"');
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

    protected function getState(): State
    {
        return $this->stateManager->get($this->getNamespace());
    }

    protected function getBlock(): AbstractBlock
    {
        return $this->getComponent()->getViewModel()->getBlock();
    }

    public function getResourceModel(): ?AbstractDb
    {
        $block = $this->getComponent()->getBlock();
        $resourceModelClass = $block->getResourceModel();
        if (empty($resourceModelClass)) {
            $resourceModelClass = $this->getProviderHandler()->getResourceModelClass($this->getProvider());
        }

        if (empty($resourceModelClass)) {
            return null;
        }

        $resourceModel = $this->objectManager->get($resourceModelClass);
        if (false === $resourceModel instanceof AbstractDb) {
            return null;
        }

        return $resourceModel;
    }
}
