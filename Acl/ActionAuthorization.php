<?php declare(strict_types=1);

namespace Loki\AdminComponents\Acl;

use Loki\AdminComponents\Ui\Button\ButtonInterface;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\View\Element\AbstractBlock;

class ActionAuthorization
{
    public const SAVE_ACL_RESOURCE = 'save_acl_resource';
    public const DELETE_ACL_RESOURCE = 'delete_acl_resource';

    private const ACTION_ARGUMENTS = [
        'save' => self::SAVE_ACL_RESOURCE,
        'duplicate' => self::SAVE_ACL_RESOURCE,
        'inline_edit' => self::SAVE_ACL_RESOURCE,
        'delete' => self::DELETE_ACL_RESOURCE,
    ];

    private const BUTTON_ACTIONS = [
        'new' => 'save',
        'saveAndContinue' => 'save',
        'saveAndClose' => 'save',
        'saveAndDuplicate' => 'duplicate',
        'delete' => 'delete',
    ];

    public function __construct(
        private readonly AuthorizationInterface $authorization
    ) {
    }

    public function getAclResource(AbstractBlock $block, string $action): string
    {
        $argumentName = self::ACTION_ARGUMENTS[$action] ?? null;
        if ($argumentName === null) {
            return '';
        }

        $aclResource = $block->getData($argumentName);

        return is_string($aclResource) ? $aclResource : '';
    }

    public function isAllowed(AbstractBlock $block, string $action): bool
    {
        $aclResource = $this->getAclResource($block, $action);
        if ($aclResource === '') {
            return true;
        }

        return $this->authorization->isAllowed($aclResource);
    }

    public function isButtonAllowed(AbstractBlock $block, ButtonInterface $button): bool
    {
        $action = self::BUTTON_ACTIONS[$button->getId()] ?? null;
        if ($action === null) {
            return true;
        }

        return $this->isAllowed($block, $action);
    }
}
