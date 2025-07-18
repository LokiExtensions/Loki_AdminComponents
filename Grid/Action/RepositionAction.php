<?php declare(strict_types=1);

namespace Loki\AdminComponents\Grid\Action;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Ui\Api\BookmarkRepositoryInterface;
use Loki\AdminComponents\Component\Grid\GridRepository;
use Loki\AdminComponents\Grid\BookmarkLoader;

class RepositionAction implements ActionInterface
{
    public function __construct(
        private BookmarkRepositoryInterface $bookmarkRepository,
        private BookmarkLoader $bookmarkLoader,
    ) {
    }

    public function execute(GridRepository $gridRepository, array $value): void
    {
        if (empty($value['reposition'])) {
            return;
        }

        $namespace = (string)$gridRepository->getComponent()->getBlock()->getNamespace();
        if (empty($namespace)) {
            return;
        }

        try {
            $bookmark = $this->bookmarkLoader->getBookmark($namespace);
        } catch (NoSuchEntityException $e) {
            return;
        }

        $propertyName = $value['reposition']['property'];
        $propertyPosition = $value['reposition']['position'];

        $bookmarkData = $bookmark->getConfig();
        if (isset($bookmarkData['views']['default']['data']['positions'])) {
            $positions = $bookmarkData['views']['default']['data']['positions'];

            $increaseCurrentPosition = false;
            foreach ($positions as $columnName => $position) {
                if ($position === $propertyPosition) {
                    $increaseCurrentPosition = true;
                }

                if ($increaseCurrentPosition) {
                    $positions[$columnName] = $position++;
                }
            }
        }

        $positions[$propertyName] = $propertyPosition;
        asort($positions);

        $positions = array_reverse($positions);
        $positions = array_reverse($positions);

        $bookmarkData['views']['default']['data']['positions'] = $positions;

        $bookmark->setConfig(json_encode($bookmarkData));
        $this->bookmarkRepository->save($bookmark);
    }
}
