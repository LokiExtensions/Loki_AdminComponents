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
        $buttonDefinitions = $block->getButtons();
        if (empty($buttonDefinitions)) {
            $buttonDefinitions = $block->getButtonActions();
        }

        if (empty($buttonDefinitions)) {
            return $buttons;
        }

        foreach ($buttonDefinitions as $buttonDefinitionIndex => $buttonDefinition) {
            if ($buttonDefinition instanceof ButtonInterface) {
                $buttons[] = $buttonDefinition;
                continue;
            }

            if (!is_array($buttonDefinition)) {
                continue;
            }

            if (!isset($buttonDefinition['id'])) {
                $buttonDefinition['id'] = $buttonDefinitionIndex;
            }

            if (array_key_exists($buttonDefinition['id'], $buttons)) {
                /** @var ButtonInterface $button */
                $button = $buttons[$buttonDefinition['id']];
                if (!empty($buttonDefinition['label'])) {
                    $button->setLabel($buttonDefinition['label']);
                }

                if (!empty($buttonDefinition['url'])) {
                    $button->setUrl($buttonDefinition['url']);
                }

                if (!empty($buttonDefinition['cssClass'])) {
                    $button->setCssClass($buttonDefinition['cssClass']);
                }

                if (!empty($buttonDefinition['method'])) {
                    $button->setMethod($buttonDefinition['method']);
                }

                if (!empty($buttonDefinition['subButtons'])) {
                    $button->setSubButtons($buttonDefinition['subButtons']);
                }

                continue;
            }

            if (!isset($buttonDefinition['method']) || !isset($buttonDefinition['label'])) {
                continue;
            }

            $buttons[] = $this->buttonFactory->create(
                id: (string)$buttonDefinition['id'] ?? (string)$buttonDefinition['method'],
                method: (string)$buttonDefinition['method'],
                label: (string)$buttonDefinition['label'],
                cssClass: isset($buttonDefinition['cssClass']) ? (string)$buttonDefinition['cssClass'] : '',
                url: isset($buttonDefinition['url']) ? (string)$buttonDefinition['url'] : '',
                subButtons: isset($buttonDefinition['subButtons']) ? (string)$buttonDefinition['subButtons'] : [],
                primary: $buttonDefinition['primary'] ?? false,
            );
        }

        return $buttons;
    }
}
