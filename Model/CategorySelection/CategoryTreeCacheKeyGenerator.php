<?php

declare(strict_types=1);

namespace Loki\AdminComponents\Model\CategorySelection;

use Magento\Backend\Model\Auth\Session;

class CategoryTreeCacheKeyGenerator
{
    public const CACHE_TAG_PREFIX = 'loki_admin_components_form_category_tree';

    public function __construct(
        private readonly Session $authSession,
    ) {
    }

    public function generate(int $storeId, ?string $filter): string
    {
        $parts = [
            self::CACHE_TAG_PREFIX,
            (string) $storeId,
            $this->getUserRole(),
            $filter ?? '',
        ];

        return implode('_', array_filter($parts));
    }

    private function getUserRole(): string
    {
        $user = $this->authSession->getUser();

        return (string)$user?->getAclRole();
    }
}
