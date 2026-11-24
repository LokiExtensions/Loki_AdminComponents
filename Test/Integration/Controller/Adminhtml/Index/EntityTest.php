<?php declare(strict_types=1);

namespace Loki\AdminComponents\Test\Integration\Controller\Adminhtml\Index;

use Magento\Framework\App\Response\Http as HttpResponse;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\BlockInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\TestCase\AbstractBackendController;

/**
 * @magentoAppArea adminhtml
 */
class EntityTest extends AbstractBackendController
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = 'Loki_AdminComponents::entity';
        $this->uri = 'backend/loki_admin_components/index/entity';
    }

    public function testRendersEntitySelectGridForAdmin(): void
    {
        $this->dispatch($this->uri);

        $response = $this->getResponse();
        $this->assertInstanceOf(HttpResponse::class, $response);
        $this->assertSame(200, $response->getHttpResponseCode());
        $this->assertInstanceOf(AbstractBlock::class, $this->getEntitySelectBlock());
    }

    public function testDoesNotRenderEntitySelectGridWithoutAdminLogin(): void
    {
        $this->_auth->logout();
        $this->dispatch($this->uri);

        $this->assertFalse($this->getEntitySelectBlock(), 'Anonymous visitors get the entity select grid');
    }

    private function getEntitySelectBlock(): BlockInterface|false
    {
        return $this->_objectManager->get(LayoutInterface::class)->getBlock('loki_admin_components.entity_select');
    }
}
