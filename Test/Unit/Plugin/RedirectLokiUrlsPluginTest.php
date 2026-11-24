<?php declare(strict_types=1);

namespace Loki\AdminComponents\Test\Unit\Plugin;

use Loki\AdminComponents\Plugin\RedirectLokiUrlsPlugin;
use Loki\AdminComponents\Util\RedirectUrlValidator;
use Magento\Framework\App\FrontController;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Redirect;
use PHPUnit\Framework\TestCase;

class RedirectLokiUrlsPluginTest extends TestCase
{
    public function testValidRedirectUrlIsApplied(): void
    {
        $result = $this->createMock(Redirect::class);
        $result->expects($this->once())->method('setUrl')->with('https://admin.example.com/admin/');

        $this->getPlugin('https://admin.example.com/admin/', true)
            ->afterDispatch($this->createStub(FrontController::class), $result);
    }

    public function testInvalidRedirectUrlIsIgnored(): void
    {
        $result = $this->createMock(Redirect::class);
        $result->expects($this->never())->method('setUrl');

        $this->getPlugin('https://evil.example/', false)
            ->afterDispatch($this->createStub(FrontController::class), $result);
    }

    public function testNonRedirectResultIsReturnedAsIs(): void
    {
        $result = new \stdClass();
        $plugin = $this->getPlugin('https://admin.example.com/admin/', true);

        $this->assertSame($result, $plugin->afterDispatch($this->createStub(FrontController::class), $result));
    }

    private function getPlugin(string $redirectUrl, bool $isValid): RedirectLokiUrlsPlugin
    {
        $request = $this->createStub(RequestInterface::class);
        $request->method('getParam')->willReturnMap([['loki_redirect_url', null, $redirectUrl]]);

        $redirectUrlValidator = $this->createStub(RedirectUrlValidator::class);
        $redirectUrlValidator->method('isValid')->willReturn($isValid);

        return new RedirectLokiUrlsPlugin($request, $redirectUrlValidator);
    }
}
