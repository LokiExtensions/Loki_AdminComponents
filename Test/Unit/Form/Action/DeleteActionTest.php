<?php declare(strict_types=1);

namespace Loki\AdminComponents\Test\Unit\Form\Action;

use Loki\AdminComponents\Component\Form\FormRepository;
use Loki\AdminComponents\Exception\FormValidationException;
use Loki\AdminComponents\Form\Action\DeleteAction;
use Loki\AdminComponents\Form\Item\ItemDeleteValidatorInterface;
use Loki\AdminComponents\ProviderHandler\ProviderHandlerInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Message\Manager;
use PHPUnit\Framework\TestCase;

class DeleteActionTest extends TestCase
{
    public function testItemIsDeletedWithoutDeleteValidators(): void
    {
        $item = new DataObject(['id' => 1]);
        $providerHandler = $this->createMock(ProviderHandlerInterface::class);
        $providerHandler->expects($this->once())->method('deleteItem')->with($this->anything(), $item);

        $deleteAction = new DeleteAction($this->createStub(Manager::class));
        $deleteAction->execute($this->getFormRepository($item, $providerHandler, []), []);
    }

    public function testFailingDeleteValidatorPreventsDeletion(): void
    {
        $item = new DataObject(['id' => 1]);
        $providerHandler = $this->createMock(ProviderHandlerInterface::class);
        $providerHandler->expects($this->never())->method('deleteItem');

        $deleteValidator = $this->createStub(ItemDeleteValidatorInterface::class);
        $deleteValidator->method('validateDelete')->willReturn(['Not allowed']);

        $messageManager = $this->createMock(Manager::class);
        $messageManager->expects($this->once())->method('addErrorMessage')->with('Not allowed');

        $this->expectException(FormValidationException::class);
        $deleteAction = new DeleteAction($messageManager);
        $deleteAction->execute($this->getFormRepository($item, $providerHandler, [$deleteValidator]), []);
    }

    private function getFormRepository(
        DataObject $item,
        ProviderHandlerInterface $providerHandler,
        array $deleteValidators
    ): FormRepository {
        $formRepository = $this->createStub(FormRepository::class);
        $formRepository->method('getProvider')->willReturn(new \stdClass());
        $formRepository->method('getProviderHandler')->willReturn($providerHandler);
        $formRepository->method('getItemFromData')->willReturn($item);
        $formRepository->method('getItemDeleteValidators')->willReturn($deleteValidators);

        return $formRepository;
    }
}
