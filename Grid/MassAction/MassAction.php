<?php declare(strict_types=1);

namespace Loki\AdminComponents\Grid\MassAction;

use Magento\Framework\UrlFactory;

class MassAction implements MassActionInterface
{
    public function __construct(
        protected UrlFactory $urlFactory,
        protected string $label,
        protected string $url,
        protected array $urlParameters = [],
    ) {
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getUrl(): string
    {
        return $this->urlFactory->create()->getUrl($this->url, $this->getUrlParameters());
    }

    protected function getUrlParameters(): array
    {
        return $this->getUrlParameters();
    }
}
