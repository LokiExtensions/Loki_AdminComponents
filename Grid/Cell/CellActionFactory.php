<?php declare(strict_types=1);

namespace Loki\AdminComponents\Grid\Cell;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\UrlFactory;

class CellActionFactory
{
    public function __construct(
        private ObjectManagerInterface $objectManager,
        private UrlFactory $urlFactory,
    ) {
    }

    public function createFromData(array $data): CellAction
    {
        $params = [];
        if (isset($data['id'])) {
            $params['id'] = $data['id'];
        }

        $url = null;
        if (isset($data['url'])) {
            $url = $this->urlFactory->create()->getUrl($data['url'], $params);
        }

        $jsMethod = null;
        if (isset($data['jsMethod'])) {
            $jsMethod = $data['jsMethod'];
        }

        $alpineMethod = null;
        if (isset($data['alpineMethod'])) {
            $alpineMethod = $data['alpineMethod'];
        }

        return $this->create(
            (string)$data['label'],
            $url,
            $jsMethod,
            $alpineMethod,
            (bool)($data['post'] ?? false),
            (string)($data['confirm'] ?? '')
        );
    }

    public function create(
        string $label,
        ?string $url = null,
        ?string $jsMethod = null,
        ?string $alpineMethod = null,
        bool $post = false,
        string $confirm = '',
    ): CellAction {
        return $this->objectManager->create(CellAction::class, [
            'label'        => $label,
            'url'          => $url,
            'jsMethod'     => $jsMethod,
            'alpineMethod' => $alpineMethod,
            'post'         => $post,
            'confirm'      => $confirm,
        ]);
    }
}
