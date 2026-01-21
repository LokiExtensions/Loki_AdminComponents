<?php declare(strict_types=1);

namespace Loki\AdminComponents\ViewModel\Form\Field;

use Loki\AdminComponents\Component\Grid\GridRepository;
use Loki\AdminComponents\Component\Grid\GridViewModel;
use Loki\AdminComponents\Form\Field\Field;
use Loki\Components\Component\Component;
use Loki\Components\Component\ComponentContext;
use Magento\Customer\Model\ResourceModel\Customer;
use Magento\Customer\Model\ResourceModel\Customer\Collection;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\View\Element\Template;

class EntitySelect implements ArgumentInterface
{
    public function __construct(
        private AbstractBlock $block
    ) {
    }

    public function getField(): Field
    {
        return $this->block->getField();
    }

    public function getButtonLabel(): string
    {
        $buttonLabel = $this->getField()->getButtonLabel();
        if (!empty($buttonLabel)) {
            return $buttonLabel;
        }

        return 'Select entity';
    }

    public function getColumns(): array
    {
        return $this->getGridViewModel()->getColumns();
    }

    public function getCurrentItem()
    {
        $currentId = 1;
        $currentItem = $this->getGridViewModel()->getCurrentItem($currentId);
        return $currentItem;
    }

    private function getGridViewModel(): GridViewModel
    {
        $block = $this->block->getLayout()->createBlock(Template::class);

        /** @var Component $component */
        $namespace = $this->getField()->getNamespace();
        if ($namespace) {
            $block->setNamespace($namespace);
        }

        $block->setResourceModel(Customer::class);
        $block->setProvider(ObjectManager::getInstance()->get(Collection::class));

        $component = ObjectManager::getInstance()->create(Component::class, [
            'name' => $block->getNameInLayout(),
            'viewModelClass' => GridViewModel::class,
            'repositoryClass' => GridRepository::class,
            'context' => ObjectManager::getInstance()->create(ComponentContext::class),
        ]);

        /** @var GridViewModel $gridViewModel */
        $gridViewModel = $component->getViewModel();
        return $gridViewModel;
    }
}
