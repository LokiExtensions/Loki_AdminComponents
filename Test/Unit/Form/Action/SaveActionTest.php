<?php declare(strict_types=1);

namespace Loki\AdminComponents\Test\Unit\Form\Action;

use Loki\AdminComponents\Component\Form\FormRepository;
use Loki\AdminComponents\Component\Form\FormViewModel;
use Loki\AdminComponents\Form\Action\SaveAction;
use Loki\AdminComponents\Form\Field\Field;
use Loki\AdminComponents\Form\Field\FieldType\Input;
use Loki\AdminComponents\Form\Field\FieldType\View;
use Loki\AdminComponents\Form\Fieldset\Fieldset;
use Loki\AdminComponents\ProviderHandler\ProviderHandlerInterface;
use Loki\Components\Component\ComponentInterface;
use Loki\Components\Util\CamelCaseConvertor;
use Magento\Framework\DataObject;
use Magento\Framework\Message\Manager;
use Magento\Framework\View\Element\Template;
use PHPUnit\Framework\TestCase;

class SaveActionTest extends TestCase
{
    public function testOnlyEditableFieldsAreSaved(): void
    {
        $item = new DataObject(['class_id' => 3, 'class_name' => 'Old', 'class_type' => 'PRODUCT']);

        $providerHandler = $this->createMock(ProviderHandlerInterface::class);
        $providerHandler->expects($this->once())->method('saveItem')->with($this->anything(), $item);

        $saveAction = new SaveAction($this->createStub(Manager::class), new CamelCaseConvertor());
        $saveAction->execute($this->getFormRepository($item, $providerHandler), [
            'item' => [
                'class_id' => 4,
                'class_name' => 'New',
                'class_type' => 'CUSTOMER',
                'id_field_name' => 'foo',
                'data' => ['bar'],
                'id' => 5,
            ],
        ]);

        $this->assertSame(
            ['class_id' => 3, 'class_name' => 'New', 'class_type' => 'PRODUCT'],
            $item->getData()
        );
    }

    public function testInvisibleHiddenAndNonEditableFieldsAreNotSaved(): void
    {
        $item = new DataObject(['class_id' => 3, 'class_name' => 'Old', 'secret' => 'a', 'hidden' => 'b', 'locked' => 'c', 'opted_in' => 'd']);

        $providerHandler = $this->createMock(ProviderHandlerInterface::class);
        $providerHandler->expects($this->once())->method('saveItem');

        $saveAction = new SaveAction($this->createStub(Manager::class), new CamelCaseConvertor());
        $saveAction->execute($this->getFormRepository($item, $providerHandler, [
            new Field(null, ['code' => 'secret', 'visible' => false, 'field_type' => $this->createStub(Input::class)]),
            new Field(null, ['code' => 'hidden', 'hidden' => true, 'field_type' => $this->createStub(Input::class)]),
            new Field(null, ['code' => 'locked', 'editable' => false, 'field_type' => $this->createStub(Input::class)]),
            new Field(null, ['code' => 'opted_in', 'hidden' => true, 'editable' => true, 'field_type' => $this->createStub(Input::class)]),
        ]), [
            'item' => ['class_name' => 'New', 'secret' => 'x', 'hidden' => 'y', 'locked' => 'z', 'opted_in' => 'w'],
        ]);

        $this->assertSame(
            ['class_id' => 3, 'class_name' => 'New', 'secret' => 'a', 'hidden' => 'b', 'locked' => 'c', 'opted_in' => 'w'],
            $item->getData()
        );
    }

    private function getFormRepository(
        DataObject $item,
        ProviderHandlerInterface $providerHandler,
        array $extraFields = []
    ): FormRepository {
        $fieldset = new Fieldset('base', '', [
            new Field(null, ['code' => 'class_id', 'field_type' => $this->createStub(View::class)]),
            new Field(null, ['code' => 'class_name', 'field_type' => $this->createStub(Input::class)]),
            new Field(null, ['code' => 'class_type', 'field_type' => $this->createStub(View::class)]),
            ...$extraFields,
        ]);

        $viewModel = $this->createStub(FormViewModel::class);
        $viewModel->method('getFieldsets')->willReturn([$fieldset]);
        $viewModel->method('getBlock')->willReturn($this->createPartialMock(Template::class, []));

        $component = $this->createStub(ComponentInterface::class);
        $component->method('getViewModel')->willReturn($viewModel);

        $formRepository = $this->createStub(FormRepository::class);
        $formRepository->method('getPrimaryKey')->willReturn('class_id');
        $formRepository->method('getProvider')->willReturn(new \stdClass());
        $formRepository->method('getProviderHandler')->willReturn($providerHandler);
        $formRepository->method('getItemFromData')->willReturn($item);
        $formRepository->method('getComponent')->willReturn($component);
        $formRepository->method('getItemValidators')->willReturn([]);

        return $formRepository;
    }
}
