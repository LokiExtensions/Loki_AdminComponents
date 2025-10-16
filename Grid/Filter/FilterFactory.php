<?php declare(strict_types=1);

namespace Loki\AdminComponents\Grid\Filter;

use Loki\AdminComponents\Exception\CreateGridFilterException;
use Magento\Framework\ObjectManagerInterface;

class FilterFactory
{
    public function __construct(
        private ObjectManagerInterface $objectManager,
    ) {
    }

    public function create(
        string $label,
        string $code,
        string $conditionType = '',
        array $data = [],
    ): Filter
    {
        return $this->objectManager->create(Filter::class, [
            'label' => $label,
            'code' => $code,
            'conditionType' => $conditionType,
            'data' => $data,
        ]);
    }

    public function createFromArray($data): Filter
    {
        if (false === isset($data['label'])) {
            throw new CreateGridFilterException('Grid filter requires a "label"');
        }

        if (false === isset($data['code'])) {
            throw new CreateGridFilterException('Grid filter requires a "code"');
        }

        return $this->create(
            $data['label'],
            $data['code'],
            '',
            $data
        );
    }
}
