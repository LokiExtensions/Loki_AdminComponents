<?php declare(strict_types=1);

namespace Yireo\LokiAdminComponents\Component\Form;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\DataObject;
use Yireo\LokiAdminComponents\Component\Form\FormRepository\ActionInterface;
use Yireo\LokiAdminComponents\Form\FormDataProvider\FormDataProviderInterface;
use Yireo\LokiComponents\Component\ComponentRepository;

class FormRepository extends ComponentRepository
{
    public function __construct(
        private RequestInterface $request,
        private array $actions = []
    ) {
    }

    public function getDataProvider(): FormDataProviderInterface
    {
        return $this->getComponent()->getViewModel()->getDataProvider();
    }

    public function getValue(): mixed
    {
        $idParam = (string)$this->getComponent()->getViewModel()->getBlock()->getIdParam();
        if (empty($idParam)) {
            $idParam = 'id';
        }

        $id = (int)$this->request->getParam($idParam);

        return $this->getDataProvider()->getItem($id);
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
            return $this->getDataProvider()->create();
        }

        $id = (int)$data['item'][$this->getPrimaryKey()];
        return $this->getDataProvider()->getItem($id);
    }

    public function getPrimaryKey(): string
    {
        if (method_exists($this->getDataProvider(), 'getPrimaryKey')) {
            return call_user_func([$this->getDataProvider(), 'getPrimaryKey']);
        }

        return 'id';
    }
}
