<?php declare(strict_types=1);

namespace Loki\AdminComponents\Filter;

use Loki\AdminComponents\Component\Form\FormRepository;
use Loki\AdminComponents\Component\Form\FormViewModel;
use Loki\AdminComponents\Form\Field\Field;
use Loki\Components\Component\ComponentInterface;
use Loki\Components\Filter\FilterInterface;
use Loki\Components\Filter\FilterScope;

class Security implements FilterInterface
{
    public function filter(mixed $value, FilterScope $scope): mixed
    {
       if (is_object($value)) {
            return $value;
        }

        $value = (string)$value;
        if ($this->allowStripTags($value, $scope)) {
            $value = strip_tags($value);
        }

        $value = htmlspecialchars_decode($value);
        return $value;
    }

    private function allowStripTags(string $value, FilterScope $scope): bool
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

        $field = $this->getFieldByPropertyName($repository, $property);
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
