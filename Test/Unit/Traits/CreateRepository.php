<?php declare(strict_types=1);

namespace Loki\AdminComponents\Test\Unit\Traits;

use Loki\AdminComponents\Component\Form\FormRepository;
use Loki\AdminComponents\Component\Grid\GridRepository;
use Loki\AdminComponents\Exception\NoProviderException;
use Loki\Components\Component\ComponentRepository;
use Loki\Components\Component\ComponentViewModelInterface;
use Magento\Framework\View\Element\Template;
use PHPUnit\Framework\MockObject\MockObject;

trait CreateRepository
{
    /**
     * @param class-string<ComponentRepository> $repositoryClass
     */
    protected function createRepository(
        string $repositoryClass = FormRepository::class,
        array $blockData = [],
        ?object $provider = null,
    ): ComponentRepository&MockObject {
        $block = $this->createPartialMock(Template::class, []);
        $block->setData($blockData);

        $viewModel = $this->createMock(ComponentViewModelInterface::class);
        $viewModel->method('getBlock')->willReturn($block);

        $repository = $this->createMock($repositoryClass);
        $repository->method('getViewModel')->willReturn($viewModel);

        if (in_array($repositoryClass, [FormRepository::class, GridRepository::class], true)) {
            if ($provider) {
                $repository->method('getProvider')->willReturn($provider);
            } else {
                $repository->method('getProvider')->willThrowException(new NoProviderException('No provider'));
            }
        }

        return $repository;
    }
}
