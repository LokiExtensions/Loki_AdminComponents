<?php declare(strict_types=1);

namespace Loki\AdminComponents\Grid\Action;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Ui\Api\BookmarkRepositoryInterface;
use Loki\AdminComponents\Component\Grid\GridRepository;
use Loki\AdminComponents\Component\Grid\GridViewModel;
use Loki\AdminComponents\Grid\BookmarkLoader;

class RepositionAction implements ActionInterface
{
    public function __construct(
        private BookmarkRepositoryInterface $bookmarkRepository,
        private BookmarkLoader $bookmarkLoader,
        private SerializerInterface $serializer,
    ) {
    }

    public function execute(GridRepository $gridRepository, array $value): void
    {
        if (empty($value['reposition']) || !is_array($value['reposition'])) {
            return;
        }

        $propertyName = $value['reposition']['property'] ?? null;
        $propertyPosition = $value['reposition']['position'] ?? null;
        if (!is_string($propertyName) || !is_numeric($propertyPosition) || (int)$propertyPosition < 0) {
            return;
        }

        $propertyPosition = (int)$propertyPosition;
        if (false === $this->isAvailableColumn($gridRepository, $propertyName)) {
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

        $positions = [];
        $bookmarkData = $bookmark->getConfig();
        if (isset($bookmarkData['views']['default']['data']['positions'])) {
            $positions = (array)$bookmarkData['views']['default']['data']['positions'];

            $increaseCurrentPosition = false;
            foreach ($positions as $columnName => $position) {
                if ($position === $propertyPosition) {
                    $increaseCurrentPosition = true;
                }

                if ($increaseCurrentPosition) {
                    $positions[$columnName] = ++$position;
                }
            }
        }

        $positions[$propertyName] = $propertyPosition;
        asort($positions);

        $positions = array_reverse($positions);

        $bookmarkData['views']['default']['data']['positions'] = $positions;

        $bookmark->setConfig($this->serializer->serialize($bookmarkData));
        $this->bookmarkRepository->save($bookmark);
    }

    private function isAvailableColumn(GridRepository $gridRepository, string $propertyName): bool
    {
        $viewModel = $gridRepository->getComponent()->getViewModel();
        if (false === $viewModel instanceof GridViewModel) {
            return false;
        }

        return in_array($propertyName, $viewModel->getAvailableColumnCodes(), true);
    }
}
