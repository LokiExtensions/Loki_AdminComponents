<?php declare(strict_types=1);

namespace Yireo\LokiAdminComponents\Component\Form;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Element\AbstractBlock;
use RuntimeException;
use Throwable;
use Yireo\LokiAdminComponents\Form\Action\ActionInterface;
use Yireo\LokiAdminComponents\Form\Action\ActionListing;
use Yireo\LokiAdminComponents\Form\Field\Field;
use Yireo\LokiAdminComponents\ProviderHandler\ProviderHandlerInterface;
use Yireo\LokiAdminComponents\ProviderHandler\ProviderHandlerListing;
use Yireo\LokiComponents\Component\ComponentRepository;

class FormRepository extends ComponentRepository
{
    public function __construct(
        private RequestInterface $request,
        private ProviderHandlerListing $providerHandlerListing,
        private ObjectManagerInterface $objectManager,
        private ActionListing $actionListing,
    ) {
    }

    // @todo: Remove the need for this
    public function getValue(): mixed
    {
        return $this->getItem();
    }

    public function getItem(): ?DataObject
    {
        $idParam = (string)$this->getComponent()->getViewModel()->getBlock()->getIdParam();
        if (empty($idParam)) {
            $idParam = 'id';
        }

        $id = (int)$this->request->getParam($idParam);
        if ($id) {
            try {
                return $this->getProviderHandler()->getItem($this->getProvider(), $id);
            } catch (Throwable $e) {
            }
        }

        $item = $this->getFactory()->create();
        $resourceModel = $this->getResourceModel();
        if ($resourceModel) {
            // @todo: Move this into separate class TableSchema (or use core)
            $tableColumns = $resourceModel->getConnection()->describeTable($resourceModel->getMainTable());
            foreach ($tableColumns as $tableColumn) {
                $item->setData(
                    $tableColumn['COLUMN_NAME'],
                    $this->getDefaultValueFromDataType($tableColumn['DATA_TYPE'])
                );
            }
        }

        return $item;
    }

    private function getDefaultValueFromDataType(string $dataType): mixed
    {
        if (in_array($dataType, ['int', 'tinyint'])) {
            return 0;
        }

        if (in_array($dataType, ['varchar', 'text', 'smalltext', 'mediumtext'])) {
            return '';
        }

        if (in_array($dataType, ['date'])) {
            return '0000-00-00';
        }

        return null;
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
        if (!empty($id)) {
            return $this->getProviderHandler()->getItem($this->getProvider(), $id);
        }

        try {
            $factory = $this->getFactory();
        } catch(\Exception $e) {
            $factory = null;
        }

        return $this->getProviderHandler()->createItem($this->getProvider(), $factory);
    }

    // @todo: Can this be private?
    public function getPrimaryKey(): string
    {
        $resourceModel = $this->getResourceModel();
        if ($resourceModel) {
            return $resourceModel->getIdFieldName();
        }

        return 'id';
    }

    public function getProvider(): object
    {
        $provider = $this->getBlock()->getProvider();
        if (is_object($provider)) {
            return $provider;
        }

        $provider = $this->objectManager->get($provider);

        if (is_object($provider)) {
            return $provider;
        }

        throw new \RuntimeException('Empty grid provider for block "'.$this->getBlock()->getNameInLayout().'"');
    }

    private function getFactory(): object
    {
        $factoryClass = $this->getBlock()->getFactory();
        if (empty($factoryClass)) {
            throw new RuntimeException('No factory class set');
        }

        $factory = $this->objectManager->get($factoryClass);

        if (false === is_object($factory)) {
            throw new RuntimeException('Empty factory "'.$factoryClass.'"');
        }

        return $factory;
    }

    private function getBlock(): AbstractBlock
    {
        return $this->getComponent()->getViewModel()->getBlock();
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

    public function getResourceModel(): ?AbstractDb
    {
        $resourceModelClass = $this->getBlock()->getResourceModel();
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
