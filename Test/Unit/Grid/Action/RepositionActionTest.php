<?php declare(strict_types=1);

namespace Loki\AdminComponents\Test\Unit\Grid\Action;

use Loki\AdminComponents\Component\Grid\GridRepository;
use Loki\AdminComponents\Component\Grid\GridViewModel;
use Loki\AdminComponents\Grid\Action\RepositionAction;
use Loki\AdminComponents\Grid\BookmarkLoader;
use Loki\Components\Component\Component;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\Element\Template;
use Magento\Ui\Api\BookmarkRepositoryInterface;
use Magento\Ui\Api\Data\BookmarkInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class RepositionActionTest extends TestCase
{
    #[DataProvider('getInvalidRepositions')]
    public function testInvalidRepositionIsIgnored(mixed $reposition): void
    {
        $bookmarkRepository = $this->createMock(BookmarkRepositoryInterface::class);
        $bookmarkRepository->expects($this->never())->method('save');

        $this->execute($bookmarkRepository, $reposition);
    }

    public static function getInvalidRepositions(): array
    {
        return [
            'not an array' => ['foo'],
            'unknown column' => [['property' => 'password_hash', 'position' => 1]],
            'non-string property' => [['property' => ['name'], 'position' => 1]],
            'non-numeric position' => [['property' => 'name', 'position' => 'foo']],
            'negative position' => [['property' => 'name', 'position' => -1]],
        ];
    }

    public function testValidRepositionIsSaved(): void
    {
        $bookmarkRepository = $this->createMock(BookmarkRepositoryInterface::class);
        $bookmarkRepository->expects($this->once())->method('save');

        $this->execute($bookmarkRepository, ['property' => 'name', 'position' => '2']);
    }

    private function execute(BookmarkRepositoryInterface $bookmarkRepository, mixed $reposition): void
    {
        $block = $this->createPartialMock(Template::class, []);
        $block->setData('namespace', 'ns');

        $viewModel = $this->createStub(GridViewModel::class);
        $viewModel->method('getAvailableColumnCodes')->willReturn(['name', 'email']);

        $component = $this->createStub(Component::class);
        $component->method('getViewModel')->willReturn($viewModel);
        $component->method('getBlock')->willReturn($block);

        $repository = $this->createStub(GridRepository::class);
        $repository->method('getComponent')->willReturn($component);

        $bookmark = $this->createStub(BookmarkInterface::class);
        $bookmark->method('getConfig')->willReturn([]);

        $bookmarkLoader = $this->createStub(BookmarkLoader::class);
        $bookmarkLoader->method('getBookmark')->willReturn($bookmark);

        $action = new RepositionAction(
            $bookmarkRepository,
            $bookmarkLoader,
            $this->createStub(SerializerInterface::class)
        );
        $action->execute($repository, ['reposition' => $reposition]);
    }
}
