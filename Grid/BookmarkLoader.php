<?php declare(strict_types=1);

namespace Yireo\LokiAdminComponents\Grid;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Ui\Api\BookmarkManagementInterface;
use Magento\Ui\Api\Data\BookmarkInterface;
use Magento\Ui\Api\Data\BookmarkSearchResultsInterface;

class BookmarkLoader
{
    public function __construct(
        private BookmarkManagementInterface $bookmarkManagement,
    ) {
    }

    public function getBookmark(string $namespace): BookmarkInterface
    {
        /** @var BookmarkSearchResultsInterface $bookmarks */
        $bookmarks = $this->bookmarkManagement->loadByNamespace($namespace);
        if ($bookmarks->getTotalCount() < 1) {
            // @todo: Find out how to add new entries to table `ui_bookmark` without actual UiComponent XML
            throw new NoSuchEntityException();
        }

        foreach ($bookmarks->getItems() as $bookmark) {
            if ($bookmark->isCurrent()) {
                return $bookmark;
            }
        }

        foreach ($bookmarks->getItems() as $bookmark) {
            if ($bookmark->getIdentifier() === 'default') {
                return $bookmark;
            }
        }

        throw new NoSuchEntityException();
    }
}
