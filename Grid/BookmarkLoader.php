<?php declare(strict_types=1);

namespace Yireo\LokiAdminComponents\Grid;

use Magento\Ui\Api\BookmarkManagementInterface;
use Magento\Ui\Api\Data\BookmarkSearchResultsInterface;
use Magento\Ui\Api\Data\BookmarkSearchResultsInterfaceFactory;

class BookmarkLoader
{
    public function __construct(
        private BookmarkManagementInterface $bookmarkManagement,
    ) {
    }

    public function getBookmarkData(string $namespace): array
    {
        /** @var BookmarkSearchResultsInterface $bookmarks */
        $bookmarks = $this->bookmarkManagement->loadByNamespace($namespace);
        if ($bookmarks->getTotalCount() < 1) {
            return [];
        }

        foreach ($bookmarks->getItems() as $bookmark) {
            if ($bookmark->isCurrent()) {
                return $bookmark->getConfig();
            }
        }

        foreach ($bookmarks->getItems() as $bookmark) {
            if ($bookmark->getIdentifier() === 'default') {
                return $bookmark->getConfig();
            }
        }

        return [];
    }
}
