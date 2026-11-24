<?php declare(strict_types=1);

namespace Loki\AdminComponents\Test\Unit\Util;

use Loki\AdminComponents\Util\RedirectUrlValidator;
use Magento\Backend\Model\UrlInterface as BackendUrlInterface;
use Magento\Framework\Url\HostChecker;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class RedirectUrlValidatorTest extends TestCase
{
    #[DataProvider('getUrls')]
    public function testIsValid(mixed $url, bool $ownOrigin, bool $expected): void
    {
        $backendUrl = $this->createStub(BackendUrlInterface::class);
        $backendUrl->method('getBaseUrl')->willReturn('https://admin.example.com/');

        $hostChecker = $this->createStub(HostChecker::class);
        $hostChecker->method('isOwnOrigin')->willReturn($ownOrigin);

        $validator = new RedirectUrlValidator($backendUrl, $hostChecker);
        $this->assertSame($expected, $validator->isValid($url));
    }

    public static function getUrls(): array
    {
        return [
            'backend host' => ['https://admin.example.com/admin/foo/bar/', false, true],
            'backend host uppercase' => ['https://ADMIN.example.com/admin/', false, true],
            'store host' => ['https://shop.example.com/admin/', true, true],
            'relative path' => ['/admin/tax_class_manager/taxclass/grid/', false, true],
            'foreign host' => ['https://evil.example/', false, false],
            'protocol relative' => ['//evil.example/', false, false],
            'backslash trick' => ['/\\evil.example/', false, false],
            'javascript scheme' => ['javascript:alert(1)', true, false],
            'data scheme' => ['data:text/html,foo', true, false],
            'ftp scheme' => ['ftp://admin.example.com/', true, false],
            'leading whitespace' => [' https://evil.example/', false, false],
            'control character' => ["/admin/\n", false, false],
            'relative without slash' => ['admin/foo', false, false],
            'empty string' => ['', true, false],
            'not a string' => [['https://admin.example.com/'], true, false],
            'null' => [null, true, false],
        ];
    }
}
