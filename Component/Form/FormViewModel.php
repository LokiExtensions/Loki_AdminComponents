<?php declare(strict_types=1);

namespace Loki\AdminComponents\Component\Form;

use Loki\AdminComponents\Form\Fieldset\FieldsetsResolver;
use Loki\AdminComponents\Form\Item\ItemResolver;
use Loki\AdminComponents\Ui\Button\ButtonsResolver;
use Loki\AdminComponents\Form\Field\Field;
use Loki\AdminComponents\Form\Field\FieldFactory;
use Loki\AdminComponents\Form\Field\FieldsResolver;
use Loki\AdminComponents\Form\Fieldset\Fieldset;
use Loki\AdminComponents\Form\Fieldset\FieldsetFactory;
use Loki\AdminComponents\Ui\Button\Button;
use Loki\AdminComponents\Ui\Button\ButtonFactory;
use Loki\Components\Component\ComponentViewModel;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\UrlFactory;
use Magento\Tests\NamingConvention\true\string;

/**
 * @method FormRepository getRepository()
 * @method FormContext getContext()
 */
class FormViewModel extends ComponentViewModel
{
    public function __construct(
        protected UrlFactory $urlFactory,
        protected ButtonFactory $buttonFactory,
        protected ObjectManagerInterface $objectManager,
        protected FieldFactory $fieldFactory,
        protected FieldsetFactory $fieldsetFactory,
        protected RequestInterface $request,
        protected FieldsetsResolver $fieldsetsResolver,
        protected FieldsResolver $fieldsResolver,
        protected ButtonsResolver $buttonsResolver,
        protected ItemResolver $itemResolver,
        protected array $itemFilters = [],
    ) {
    }

    public function getJsComponentName(): ?string
    {
        return 'LokiAdminFormComponent';
    }

    public function getJsData(): array
    {
        return [
            ...parent::getJsData(),
            'aclResource' => $this->getAclResource(),
            'item' => $this->getItemData(),
            'indexUrl' => $this->getIndexUrl(),
        ];
    }

    private function getItemData(): array
    {
        $item = $this->itemResolver->resolve(
            $this->getRepository(),
            $this->getBlock()
        );

        return $item->getData();
    }

    public function getIndexUrl(): string
    {
        return $this->urlFactory->create()->getUrl($this->getIndexUri());
    }

    /**
     * @return Button[]
     */
    public function getButtons(): array
    {
        return $this->buttonsResolver->resolve(
            $this->getRepository(),
            $this->getBlock(),
        );
    }

    /**
     * @return Fieldset[]
     */
    public function getFieldsets(): array
    {
        return $this->fieldsetsResolver->resolve(
            $this->getRepository(),
            $this->getBlock(),
        );
    }

    /**
     * @return Field[]
     * @throws LocalizedException
     * @deprecated Use getFieldsets() instead
     */
    public function getFields(): array
    {
        return $this->fieldsResolver->resolve(
            $this->getRepository(),
            (array)$this->getBlock()->getFields(),
        );
    }

    public function getAclResource(): string
    {
        $aclResource = (string) $this->getBlock()->getAclResource();
        if (!empty($aclResource)) {
            return $aclResource;
        }

        return 'Magento_Backend::admin';
    }

    private function getIndexUri(): string
    {
        $indexUrl = $this->getBlock()->getIndexUrl();
        if (!empty($indexUrl)) {
            return $indexUrl;
        }


        $currentUri = (string)$this->request->getParam('currentUri');
        if (!empty($currentUri)) {
            $currentUriParts = explode('_', $currentUri);

            return $currentUriParts[0].'/'.$currentUriParts[1].'/index';
        }

        return '*/*/index';
    }
}
