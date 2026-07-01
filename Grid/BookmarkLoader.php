<?php declare(strict_types=1);

namespace Loki\AdminComponents\Grid;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Ui\Api\BookmarkManagementInterface;
use Magento\Ui\Api\BookmarkRepositoryInterface;
use Magento\Ui\Api\Data\BookmarkInterface;
use Magento\Ui\Api\Data\BookmarkSearchResultsInterface;

class BookmarkLoader
{
    public function __construct(
        private BookmarkManagementInterface $bookmarkManagement,
        private BookmarkRepositoryInterface $bookmarkRepository,
        private SerializerInterface $serializer,
    ) {
    }

    public function getBookmark(string $namespace, string $identifier = 'default'): BookmarkInterface
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
            if ($bookmark->getIdentifier() === $identifier) {
                return $bookmark;
            }
        }

        throw new NoSuchEntityException();
    }

    public function getBookmarkData(string $namespace, string $identifier = 'default'): array
    {
        $bookmark = $this->getBookmark($namespace, $identifier);
        $config = $bookmark->getConfig();
        return $config['views'][$identifier]['data'];
    }

    public function saveBookmark(BookmarkInterface $bookmark, array $data, string $identifier = 'default'): void
    {
        $config = $bookmark->getConfig();
        $config['views'][$identifier]['data'] = array_replace_recursive($config['views'][$identifier]['data'], $data);

        $bookmark->setConfig($this->serializer->serialize($config));
        $this->bookmarkRepository->save($bookmark);
    }
}
