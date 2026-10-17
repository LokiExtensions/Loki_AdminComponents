<?php declare(strict_types=1);

namespace Loki\AdminComponents\Test\Unit\Ui\Button\ButtonsResolver;

use Loki\AdminComponents\Component\Form\FormRepository;
use Loki\AdminComponents\Component\Grid\GridRepository;
use Loki\AdminComponents\Test\Unit\Traits\CreateButtonFactory;
use Loki\AdminComponents\Test\Unit\Traits\CreateRepository;
use Loki\AdminComponents\Ui\Button\Button;
use Loki\AdminComponents\Ui\Button\ButtonsResolver\BlockButtonsResolver;
use Loki\Components\Component\ComponentViewModelInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class BlockButtonsResolverTest extends TestCase
{
    use CreateButtonFactory;
    use CreateRepository;

    public function testWithoutBlockDefinitionsReturnsButtonsUnchanged(): void
    {
        $existing = ['close' => $this->createButton('close')];
        $repository = $this->createRepository(FormRepository::class);

        $this->assertSame($existing, $this->getResolver()->resolve($repository, $existing));
        $this->assertSame([], $this->getResolver()->resolve($repository));
    }

    public function testWithoutBlockReturnsButtonsUnchanged(): void
    {
        $viewModel = $this->createMock(ComponentViewModelInterface::class);
        $viewModel->method('getBlock')->willReturn(null);
        $repository = $this->createMock(FormRepository::class);
        $repository->method('getViewModel')->willReturn($viewModel);

        $existing = ['close' => $this->createButton('close')];
        $this->assertSame($existing, $this->getResolver()->resolve($repository, $existing));
    }

    #[DataProvider('getBlockArgumentNames')]
    public function testCreatesButtonsFromBlockArgument(string $argumentName, string $repositoryClass): void
    {
        $repository = $this->createRepository($repositoryClass, [
            $argumentName => [
                'new' => [
                    'method' => 'newAction',
                    'label' => 'Add New Tax Class',
                    'cssClass' => 'new',
                    'url' => 'https://example.com/new',
                    'primary' => true,
                ],
            ],
        ]);

        $buttons = $this->getResolver()->resolve($repository);

        $this->assertSame(['new'], array_keys($buttons));
        $button = $buttons['new'];
        $this->assertInstanceOf(Button::class, $button);
        $this->assertSame('new', $button->getId());
        $this->assertSame('newAction', $button->getMethod());
        $this->assertSame('Add New Tax Class', $button->getLabel());
        $this->assertSame('new primary', $button->getCssClass());
        $this->assertSame('https://example.com/new', $button->getUrl());
        $this->assertSame([], $button->getSubButtons());
    }

    public static function getBlockArgumentNames(): array
    {
        return [
            ['buttons', FormRepository::class],
            ['button_actions', FormRepository::class],
            ['buttons', GridRepository::class],
            ['button_actions', GridRepository::class],
        ];
    }

    public function testButtonsArgumentTakesPrecedenceOverButtonActions(): void
    {
        $repository = $this->createRepository(FormRepository::class, [
            'buttons' => ['a' => ['method' => 'aAction', 'label' => 'A']],
            'button_actions' => ['b' => ['method' => 'bAction', 'label' => 'B']],
        ]);

        $this->assertSame(['a'], array_keys($this->getResolver()->resolve($repository)));
    }

    public function testButtonObjectsArePassedThroughKeyedById(): void
    {
        $button = $this->createButton('custom');
        $repository = $this->createRepository(FormRepository::class, [
            'buttons' => [$button],
        ]);

        $this->assertSame(['custom' => $button], $this->getResolver()->resolve($repository));
    }

    public function testInvalidDefinitionsAreSkipped(): void
    {
        $repository = $this->createRepository(FormRepository::class, [
            'buttons' => [
                'string' => 'foobar',
                'no_method' => ['label' => 'No method'],
                'no_label' => ['method' => 'noLabelAction'],
                'empty_method' => ['method' => '', 'label' => 'Empty'],
                'valid' => ['method' => 'validAction', 'label' => 'Valid'],
            ],
        ]);

        $this->assertSame(['valid'], array_keys($this->getResolver()->resolve($repository)));
    }

    public function testButtonIdResolving(): void
    {
        $repository = $this->createRepository(FormRepository::class, [
            'buttons' => [
                'key' => ['method' => 'keyAction', 'label' => 'Key'],
                'ignored' => ['id' => 'explicit', 'method' => 'explicitAction', 'label' => 'Explicit'],
                0 => ['method' => 'numericAction', 'label' => 'Numeric'],
            ],
        ]);

        $buttons = $this->getResolver()->resolve($repository);

        $this->assertSame(['key', 'explicit', 'numericAction_0'], array_keys($buttons));
        foreach ($buttons as $buttonId => $button) {
            $this->assertSame((string)$buttonId, $button->getId());
        }
    }

    public function testNumericKeysDoNotOverwriteExistingListButtons(): void
    {
        $existing = [0 => $this->createButton('first')];
        $repository = $this->createRepository(FormRepository::class, [
            'buttons' => [
                0 => ['method' => 'otherAction', 'label' => 'Other'],
            ],
        ]);

        $buttons = $this->getResolver()->resolve($repository, $existing);

        $this->assertCount(2, $buttons);
        $this->assertSame($existing[0], $buttons[0]);
        $this->assertSame('Foo', $buttons[0]->getLabel());
        $this->assertSame('Other', $buttons['otherAction_0']->getLabel());
    }

    #[DataProvider('getPrimaryValues')]
    public function testPrimaryValuesAreCastToBoolean(mixed $primary, string $expectedCssClass): void
    {
        $repository = $this->createRepository(FormRepository::class, [
            'buttons' => [
                'save' => ['method' => 'saveAction', 'label' => 'Save', 'cssClass' => 'save', 'primary' => $primary],
            ],
        ]);

        $buttons = $this->getResolver()->resolve($repository);
        $this->assertSame($expectedCssClass, $buttons['save']->getCssClass());
    }

    public static function getPrimaryValues(): array
    {
        return [
            [true, 'save primary'],
            [false, 'save'],
            ['true', 'save primary'],
            ['false', 'save'],
            ['1', 'save primary'],
            ['0', 'save'],
            [1, 'save primary'],
            [0, 'save'],
            ['', 'save'],
            [null, 'save'],
        ];
    }

    public function testSubButtonsAreConvertedToButtonObjects(): void
    {
        $subButtonObject = $this->createButton('object');
        $repository = $this->createRepository(GridRepository::class, [
            'button_actions' => [
                'add' => [
                    'method' => 'redirectAction',
                    'label' => 'Add',
                    'url' => 'https://example.com/simple',
                    'subButtons' => [
                        'virtual' => [
                            'method' => 'redirectAction',
                            'label' => 'Virtual',
                            'url' => 'https://example.com/virtual',
                        ],
                        0 => [
                            'method' => 'redirectAction',
                            'label' => 'Bundle',
                            'url' => 'https://example.com/bundle',
                        ],
                        1 => [
                            'method' => 'redirectAction',
                            'label' => 'Grouped',
                            'url' => 'https://example.com/grouped',
                        ],
                        'invalid' => ['label' => 'Invalid'],
                        $subButtonObject,
                    ],
                ],
            ],
        ]);

        $buttons = $this->getResolver()->resolve($repository);
        $subButtons = $buttons['add']->getSubButtons();

        $this->assertSame(
            ['virtual', 'redirectAction_0', 'redirectAction_1', 'object'],
            array_keys($subButtons)
        );

        $this->assertInstanceOf(Button::class, $subButtons['virtual']);
        $this->assertSame('Virtual', $subButtons['virtual']->getLabel());
        $this->assertSame('https://example.com/virtual', $subButtons['virtual']->getUrl());
        $this->assertSame('https://example.com/bundle', $subButtons['redirectAction_0']->getUrl());
        $this->assertSame('https://example.com/grouped', $subButtons['redirectAction_1']->getUrl());
        $this->assertSame($subButtonObject, $subButtons['object']);
    }

    public function testInvalidSubButtonsValueIsIgnored(): void
    {
        $repository = $this->createRepository(FormRepository::class, [
            'buttons' => [
                'add' => ['method' => 'addAction', 'label' => 'Add', 'subButtons' => 'foobar'],
            ],
        ]);

        $buttons = $this->getResolver()->resolve($repository);
        $this->assertSame([], $buttons['add']->getSubButtons());
    }

    public function testExistingButtonIsOverriddenById(): void
    {
        $existing = $this->createButtonFactory()->createSaveCloseAction();
        $repository = $this->createRepository(FormRepository::class, [
            'buttons' => [
                'saveAndClose' => [
                    'label' => 'Store & Close',
                    'url' => 'https://example.com/save',
                    'cssClass' => 'store',
                    'method' => 'storeAction',
                    'subButtons' => [
                        'sub' => ['method' => 'subAction', 'label' => 'Sub'],
                    ],
                ],
            ],
        ]);

        $buttons = $this->getResolver()->resolve($repository, ['saveAndClose' => $existing]);

        $this->assertCount(1, $buttons);
        $this->assertSame($existing, $buttons['saveAndClose']);
        $this->assertSame('Store & Close', $existing->getLabel());
        $this->assertSame('https://example.com/save', $existing->getUrl());
        $this->assertSame('store', $existing->getCssClass());
        $this->assertSame('storeAction', $existing->getMethod());
        $this->assertSame(['sub'], array_keys($existing->getSubButtons()));
        $this->assertInstanceOf(Button::class, $existing->getSubButtons()['sub']);
    }

    public function testPartialOverrideKeepsOtherProperties(): void
    {
        $existing = $this->createButtonFactory()->createSaveCloseAction();
        $repository = $this->createRepository(FormRepository::class, [
            'buttons' => [
                'saveAndClose' => ['label' => 'Store & Close'],
            ],
        ]);

        $this->getResolver()->resolve($repository, ['saveAndClose' => $existing]);

        $this->assertSame('Store & Close', $existing->getLabel());
        $this->assertSame('saveAndCloseAction', $existing->getMethod());
        $this->assertSame('save primary', $existing->getCssClass());
    }

    #[DataProvider('getPrimaryOverrideData')]
    public function testOverridePrimary(string $initialCssClass, mixed $primary, string $expectedCssClass): void
    {
        $existing = $this->createButtonFactory()->create('save', 'saveAction', 'Save', $initialCssClass);
        $repository = $this->createRepository(FormRepository::class, [
            'buttons' => [
                'save' => ['primary' => $primary],
            ],
        ]);

        $this->getResolver()->resolve($repository, ['save' => $existing]);
        $this->assertSame($expectedCssClass, $existing->getCssClass());
    }

    public static function getPrimaryOverrideData(): array
    {
        return [
            ['save', true, 'save primary'],
            ['save', 'true', 'save primary'],
            ['save primary', false, 'save'],
            ['save primary', '0', 'save'],
            ['save primary', true, 'save primary'],
            ['save', false, 'save'],
        ];
    }

    private function getResolver(): BlockButtonsResolver
    {
        return new BlockButtonsResolver($this->createButtonFactory());
    }
}
