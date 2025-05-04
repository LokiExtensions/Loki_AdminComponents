<?php declare(strict_types=1);

namespace Yireo\LokiAdminComponents\Component\Form;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\DataObject;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Element\AbstractBlock;
use Yireo\LokiAdminComponents\Component\Form\FormRepository\ActionInterface;
use Yireo\LokiAdminComponents\ProviderHandler\ProviderHandlerInterface;
use Yireo\LokiAdminComponents\ProviderHandler\ProviderHandlerResolver;
use Yireo\LokiComponents\Component\ComponentRepository;

class FormRepository extends ComponentRepository
{
    public function __construct(
        private RequestInterface $request,
        private ProviderHandlerResolver $providerHandlerResolver,
        private ObjectManagerInterface $objectManager,
        private array $actions = []
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


    public function saveValue(mixed $value): void
    {
        if (!is_array($value) || !isset($value['actions']) || !is_array($value['actions'])) {
            return;
        }

        foreach ($value['actions'] as $actionName) {
            if (false === array_key_exists($actionName, $this->actions)) {
                continue;
            }

            /** @var ActionInterface $action */
            $action = $this->actions[$actionName];
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

    public function getPrimaryKey(): string
    {
        // @todo: Make this configurable

        return 'id';
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

    private function getFactory()
    {
        $factoryClass = $this->getBlock()->getFactory();
        $factory = $this->objectManager->get($factoryClass);

        if (!empty($factory)) {
            return $factory;
        }

        throw new \RuntimeException('Empty provider "'.$factoryClass.'"');
    }

    private function getBlock(): AbstractBlock
    {
        return $this->getComponent()->getViewModel()->getBlock();
    }

    private function getProviderHandler(): ProviderHandlerInterface
    {
        $providerHandlerName = (string)$this->getComponent()->getViewModel()->getBlock()->getProviderHandler();

        return $this->providerHandlerResolver->resolve($providerHandlerName);
    }
}
