<?php declare(strict_types=1);

namespace Loki\AdminComponents\ViewModel\Form\Field;

use Loki\AdminComponents\Component\Grid\GridRepository;
use Loki\AdminComponents\Component\Grid\GridViewModel;
use Loki\AdminComponents\Form\Field\Field;
use Loki\AdminComponents\Grid\Column\Column;
use Loki\Components\Component\Component;
use Loki\Components\Component\ComponentContext;
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

    public function getModalTitle(): string
    {
        $modalTitle = $this->getField()->getModalTitle();
        if (!empty($modalTitle)) {
            return $modalTitle;
        }

        return $this->getButtonLabel();
    }

    public function getColumns(): array
    {
        $columns = $this->getGridViewModel()->getVisibleColumns();
        return array_filter($columns, function (Column $column) {
            return $column->getCode() !== 'ids';
        });
    }

    public function getCurrentItem()
    {
        $currentId = 1;
        $currentItem = $this->getGridViewModel()->getCurrentItem($currentId);
        return $currentItem;
    }

    public function getJsData(): array
    {
        return [
            'ajaxUrl' => $this->getAjaxUrl(),
            'valueCode' => $this->getValueCode(),
        ];
    }

    public function getJsonData(): string
    {
        return json_encode($this->getJsData());
    }

    private function getNamespace(): string
    {
        return (string)$this->getField()->getNamespace();
    }

    private function getGridViewModel(): GridViewModel
    {
        $gridViewModel = null;
        if ($gridViewModel instanceof GridViewModel) {
            return $gridViewModel;
        }

        $block = $this->block->getLayout()->createBlock(Template::class);
        $block->setNamespace($this->getNamespace());

        $resourceModel = $this->getField()->getResourceModel();
        if ($resourceModel) {
            $block->setResourceModel($resourceModel);
        }

        $provider = $this->getField()->getProvider();
        if ($provider) {
            $block->setProvider($provider);
        }

        /** @var Component $component */
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

    public function getValueCode(): string
    {
        return $this->getField()->getScope() . '.' . $this->getField()->getCode();
    }

    public function getAjaxUrl(): string
    {
        return $this->block->getUrl('mui/index/render') . '?'.http_build_query([
                'namespace' => $this->getNamespace(),
                //'sorting[field]' => 'entity_id',
                //'sorting[direction]' => 'asc',
                'keywordUpdated' => false,
                'filters[placeholder]' => true,
                'paging[pageSize]' => 20,
                'paging[current]' => 1,
                'isAjax' => 'true',
            ]);
    }
}
