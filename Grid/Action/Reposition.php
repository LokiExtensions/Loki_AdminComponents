<?php declare(strict_types=1);

namespace Yireo\LokiAdminComponents\Grid\Action;

use Magento\Ui\Api\BookmarkRepositoryInterface;
use Yireo\LokiAdminComponents\Component\Grid\GridRepository;
use Yireo\LokiAdminComponents\Grid\BookmarkLoader;

class Reposition implements ActionInterface
{
    public function __construct(
        private BookmarkRepositoryInterface $bookmarkRepository,
        private BookmarkLoader $bookmarkLoader,
    ) {
    }

    public function execute(GridRepository $gridRepository, array $value): void
    {
        $gridRepository->getComponent()->getBlock()->getNamespace();
    }
}
