<?php declare(strict_types=1);

namespace Yireo\LokiAdminComponents\Component\Form;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\DataObject;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Element\AbstractBlock;
use RuntimeException;
use Yireo\LokiAdminComponents\Form\Action\ActionInterface;
use Yireo\LokiAdminComponents\Form\Action\ActionListing;
use Yireo\LokiAdminComponents\ProviderHandler\ProviderHandlerInterface;
use Yireo\LokiAdminComponents\ProviderHandler\ProviderHandlerResolver;
use Yireo\LokiComponents\Component\ComponentRepository;

class FormRepository extends ComponentRepository
{
    public function __construct(
        private RequestInterface $request,
        private ProviderHandlerResolver $providerHandlerResolver,
        private ObjectManagerInterface $objectManager,
        private ActionListing $actionListing,
    ) {
    }

    // @todo: Remove the need for this
    public function getValue(): mixed
    {
        return $this->getItem();
    }

    public function getItem(): DataObject
    {
        $idParam = (string)$this->getComponent()->getViewModel()->getBlock()->getIdParam();
        if (empty($idParam)) {
            $idParam = 'id';
        }

        $id = (int)$this->request->getParam($idParam);

        return $this->getProviderHandler()->getItem($this->getProvider(), $id);
    }


    // @todo: Rename saveValue to handleValue
    public function saveValue(mixed $value): void
    {
        if (!is_array($value) || !isset($value['actions']) || !is_array($value['actions'])) {
            return;
        }

        $actions = $this->actionListing->getActions();
        foreach ($value['actions'] as $actionName) {

            if (false === array_key_exists($actionName, $actions)) {
                continue;
            }

            /** @var ActionInterface $action */
            $action = $actions[$actionName];
            $action->execute($this, $value);
        }
    }

    public function getItemFromData(array $data): DataObject
    {
        if (!isset($data['item'][$this->getPrimaryKey()])) {
            return $this->getFactory()->create();
        }

        $id = (int)$data['item'][$this->getPrimaryKey()];

        return $this->getProviderHandler()->getItem($this->getProvider(), $id);
    }

    // @todo: Can this be private?
    public function getPrimaryKey(): string
    {
        // @todo: Make this configurable

        return 'id';
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

        throw new \RuntimeException('Empty grid provider for block "'.$this->getBlock()->getNameInLayout().'"');
    }

    private function getFactory()
    {
        $factoryClass = $this->getBlock()->getFactory();
        $factory = $this->objectManager->get($factoryClass);

        if (!empty($factory)) {
            return $factory;
        }

        throw new RuntimeException('Empty factory "'.$factoryClass.'"');
    }

    private function getBlock(): AbstractBlock
    {
        return $this->getComponent()->getViewModel()->getBlock();
    }

    public function getProviderHandler(): ProviderHandlerInterface
    {
        $providerHandlerName = (string)$this->getComponent()->getViewModel()->getBlock()->getProviderHandler();

        return $this->providerHandlerResolver->resolve($providerHandlerName);
    }
}
