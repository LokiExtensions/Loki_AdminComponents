<?php
declare(strict_types=1);

namespace Loki\AdminComponents\Ui\Button\ButtonsResolver;

use Loki\AdminComponents\Ui\Button\ButtonFactory;
use Loki\AdminComponents\Ui\Button\ButtonInterface;
use Loki\Components\Component\ComponentRepository;

class BlockButtonsResolver implements ButtonsResolverInterface
{
    public function __construct(
        private ButtonFactory $buttonFactory,
    ) {
    }

    public function resolve(ComponentRepository $repository, array $buttons = []): array
    {
        $block = $repository->getViewModel()->getBlock();
        if (null === $block) {
            return $buttons;
        }

        $buttonDefinitions = $block->getButtons();
        if (empty($buttonDefinitions)) {
            $buttonDefinitions = $block->getButtonActions();
        }

        if (empty($buttonDefinitions) || !is_array($buttonDefinitions)) {
            return $buttons;
        }

        foreach ($buttonDefinitions as $buttonDefinitionIndex => $buttonDefinition) {
            if ($buttonDefinition instanceof ButtonInterface) {
                $buttons[$buttonDefinition->getId()] = $buttonDefinition;
                continue;
            }

            if (!is_array($buttonDefinition)) {
                continue;
            }

            $buttonId = $this->getButtonId($buttonDefinitionIndex, $buttonDefinition);
            if (array_key_exists($buttonId, $buttons) && $buttons[$buttonId] instanceof ButtonInterface) {
                $this->updateButton($buttons[$buttonId], $buttonDefinition);
                continue;
            }

            $button = $this->createButton($buttonId, $buttonDefinition);
            if ($button instanceof ButtonInterface) {
                $buttons[$buttonId] = $button;
            }
        }

        return $buttons;
    }

    private function getButtonId(int|string $buttonDefinitionIndex, array $buttonDefinition): string
    {
        if (!empty($buttonDefinition['id'])) {
            return (string)$buttonDefinition['id'];
        }

        if (is_string($buttonDefinitionIndex)) {
            return $buttonDefinitionIndex;
        }

        // Numeric keys would clash with list-based buttons of other resolvers
        return (string)($buttonDefinition['method'] ?? 'button').'_'.$buttonDefinitionIndex;
    }

    private function createButton(string $buttonId, array $buttonDefinition): ?ButtonInterface
    {
        if (empty($buttonDefinition['method']) || empty($buttonDefinition['label'])) {
            return null;
        }

        return $this->buttonFactory->create(
            id: $buttonId,
            method: (string)$buttonDefinition['method'],
            label: (string)$buttonDefinition['label'],
            cssClass: isset($buttonDefinition['cssClass']) ? (string)$buttonDefinition['cssClass'] : '',
            url: isset($buttonDefinition['url']) ? (string)$buttonDefinition['url'] : '',
            subButtons: $this->createSubButtons($buttonDefinition['subButtons'] ?? []),
            primary: $this->toBool($buttonDefinition['primary'] ?? false),
        );
    }

    private function updateButton(ButtonInterface $button, array $buttonDefinition): void
    {
        if (!empty($buttonDefinition['label'])) {
            $button->setLabel((string)$buttonDefinition['label']);
        }

        if (!empty($buttonDefinition['url'])) {
            $button->setUrl((string)$buttonDefinition['url']);
        }

        if (!empty($buttonDefinition['cssClass'])) {
            $button->setCssClass((string)$buttonDefinition['cssClass']);
        }

        if (!empty($buttonDefinition['method'])) {
            $button->setMethod((string)$buttonDefinition['method']);
        }

        if (!empty($buttonDefinition['subButtons'])) {
            $button->setSubButtons($this->createSubButtons($buttonDefinition['subButtons']));
        }

        if (array_key_exists('primary', $buttonDefinition)) {
            $cssClasses = array_filter(
                explode(' ', $button->getCssClass()),
                fn (string $cssClass) => $cssClass !== '' && $cssClass !== 'primary'
            );

            if ($this->toBool($buttonDefinition['primary'])) {
                $cssClasses[] = 'primary';
            }

            $button->setCssClass(implode(' ', $cssClasses));
        }
    }

    /**
     * @return ButtonInterface[]
     */
    private function createSubButtons(mixed $subButtonDefinitions): array
    {
        if (!is_array($subButtonDefinitions)) {
            return [];
        }

        $subButtons = [];
        foreach ($subButtonDefinitions as $subButtonIndex => $subButtonDefinition) {
            if ($subButtonDefinition instanceof ButtonInterface) {
                $subButtons[$subButtonDefinition->getId()] = $subButtonDefinition;
                continue;
            }

            if (!is_array($subButtonDefinition)) {
                continue;
            }

            $subButtonId = $this->getButtonId($subButtonIndex, $subButtonDefinition);
            $subButton = $this->createButton($subButtonId, $subButtonDefinition);
            if ($subButton instanceof ButtonInterface) {
                $subButtons[$subButtonId] = $subButton;
            }
        }

        return $subButtons;
    }

    private function toBool(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}
