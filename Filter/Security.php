<?php declare(strict_types=1);

namespace Loki\AdminComponents\Filter;

use Loki\AdminComponents\Component\Form\FormRepository;
use Loki\AdminComponents\Component\Form\FormViewModel;
use Loki\AdminComponents\Form\Field\Field;
use Loki\Components\Component\ComponentInterface;
use Loki\Components\Filter\FilterInterface;
use Loki\Components\Filter\FilterScope;
use Loki\Components\Filter\Security as CoreSecurity;

class Security implements FilterInterface
{
    public function __construct(
        private readonly CoreSecurity $coreSecurity
    ) {
    }

    public function filter(mixed $value, FilterScope $scope): mixed
    {
        if (is_object($value) || is_array($value)) {
            return $value;
        }

        if (false === $this->allowStripTags($scope)) {
            return str_replace("\0", '', (string)$value);
        }

        return $this->coreSecurity->filter($value, $scope);
    }

    private function allowStripTags(FilterScope $scope): bool
    {
        $component = $scope->getComponent();
        if (false === $component instanceof ComponentInterface) {
            return true;
        }

        $repository = $component->getRepository();
        if (false === $repository instanceof FormRepository) {
            return true;
        }

        $property = $scope->getProperty();
        if (empty($property)) {
            return true;
        }

        $field = $this->getFieldByPropertyName($repository, (string)$property);
        if (empty($field)) {
            return true;
        }

        return !$field->allowHtml();
    }

    private function getFieldByPropertyName(FormRepository $repository, string $propertyName): ?Field
    {
        /** @var FormViewModel $viewModel */
        $viewModel = $repository->getComponent()->getViewModel();
        $fields = $viewModel->getFields();
        foreach ($fields as $field) {
            if ($field->getCode() === $propertyName) {
                return $field;
            }
        }

        return null;
    }
}
