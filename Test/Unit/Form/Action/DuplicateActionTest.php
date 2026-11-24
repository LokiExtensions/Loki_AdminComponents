<?php declare(strict_types=1);

namespace Loki\AdminComponents\Test\Unit\Form\Action;

use Loki\AdminComponents\Component\Form\FormRepository;
use Loki\AdminComponents\Exception\FormValidationException;
use Loki\AdminComponents\Form\Action\DuplicateAction;
use Loki\AdminComponents\Form\Item\ItemValidatorInterface;
use Loki\AdminComponents\ProviderHandler\ProviderHandlerInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Message\Manager;
use PHPUnit\Framework\TestCase;

class DuplicateActionTest extends TestCase
{
    public function testValidItemIsDuplicated(): void
    {
        $item = new DataObject(['id' => 1]);
        $providerHandler = $this->createMock(ProviderHandlerInterface::class);
        $providerHandler->expects($this->once())->method('duplicateItem')->with($this->anything(), $item);

        $validator = $this->createStub(ItemValidatorInterface::class);
        $validator->method('validate')->willReturn(true);

        $duplicateAction = new DuplicateAction($this->createStub(Manager::class));
        $duplicateAction->execute($this->getFormRepository($item, $providerHandler, [$validator]), []);
    }

    public function testInvalidItemIsNotDuplicated(): void
    {
        $item = new DataObject(['id' => 1]);
        $providerHandler = $this->createMock(ProviderHandlerInterface::class);
        $providerHandler->expects($this->never())->method('duplicateItem');

        $validator = $this->createStub(ItemValidatorInterface::class);
        $validator->method('validate')->willReturn(['Invalid']);

        $this->expectException(FormValidationException::class);
        $duplicateAction = new DuplicateAction($this->createStub(Manager::class));
        $duplicateAction->execute($this->getFormRepository($item, $providerHandler, [$validator]), []);
    }

    private function getFormRepository(
        DataObject $item,
        ProviderHandlerInterface $providerHandler,
        array $validators
    ): FormRepository {
        $formRepository = $this->createStub(FormRepository::class);
        $formRepository->method('getProvider')->willReturn(new \stdClass());
        $formRepository->method('getProviderHandler')->willReturn($providerHandler);
        $formRepository->method('getItemFromData')->willReturn($item);
        $formRepository->method('getItemValidators')->willReturn($validators);

        return $formRepository;
    }
}
