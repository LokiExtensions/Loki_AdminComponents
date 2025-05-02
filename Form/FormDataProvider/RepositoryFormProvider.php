<?php declare(strict_types=1);

namespace Yireo\LokiAdminComponents\Form\FormDataProvider;

use Yireo\LokiAdminComponents\RepositoryProvider\RepositoryProviderInterface;
use Magento\Framework\DataObject;
use RuntimeException;

class RepositoryFormProvider implements FormDataProviderInterface
{
    public function __construct(
        private RepositoryProviderInterface $repositoryProvider,
    ) {
    }
    public function getItem(int $id): DataObject
    {
        $repository = $this->repositoryProvider->getRepository();
        if (method_exists($repository, 'getById')) {
            return call_user_func([$repository, 'getById'], $id);
        }

        if (method_exists($repository, 'get')) {
            return call_user_func([$repository, 'get'], $id);
        }

        throw new RuntimeException('Repository has no method we know of.');
    }

    public function save(DataObject $dataObject): void
    {
        $this->repositoryProvider->getRepository()->save($dataObject);
    }

    public function delete(DataObject $dataObject): void
    {
        $this->repositoryProvider->getRepository()->delete($dataObject);
    }

    public function duplicate(DataObject $dataObject): void
    {
        $this->repositoryProvider->getRepository()->duplicate($dataObject);
    }

    public function create(): DataObject
    {
        return $this->repositoryProvider->create();
    }
}
