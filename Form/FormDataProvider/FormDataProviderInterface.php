<?php
declare(strict_types=1);

namespace Yireo\LokiAdminComponents\Form\FormDataProvider;

use Magento\Framework\DataObject;
use Magento\Framework\View\Element\Block\ArgumentInterface;

interface FormDataProviderInterface extends ArgumentInterface
{
    public function getItem(int $id): DataObject;

    public function create(): DataObject;

    public function save(DataObject $dataObject): void;

    public function duplicate(DataObject $dataObject): void;

    public function delete(DataObject $dataObject): void;
}
