<?php declare(strict_types=1);

namespace Loki\AdminComponents\Test\Unit\Util;

use Loki\AdminComponents\Util\RedirectUrlValidator;
use Magento\Backend\Model\UrlInterface as BackendUrlInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class RedirectUrlValidatorTest extends TestCase
{
    #[DataProvider('getUrls')]
    public function testIsValid(mixed $url, bool $expected): void
    {
        $backendUrl = $this->createStub(BackendUrlInterface::class);
        $backendUrl->method('getBaseUrl')->willReturn('https://admin.example.com/');

        $validator = new RedirectUrlValidator($backendUrl);
        $this->assertSame($expected, $validator->isValid($url));
    }

    public static function getUrls(): array
    {
        return [
            'backend host' => ['https://admin.example.com/admin/foo/bar/', true],
            'backend host uppercase' => ['https://ADMIN.example.com/admin/', true],
            'store host' => ['https://shop.example.com/admin/', false],
            'relative path' => ['/admin/tax_class_manager/taxclass/grid/', true],
            'foreign host' => ['https://evil.example/', false],
            'protocol relative' => ['//evil.example/', false],
            'backslash trick' => ['/\\evil.example/', false],
            'javascript scheme' => ['javascript:alert(1)', false],
            'data scheme' => ['data:text/html,foo', false],
            'ftp scheme' => ['ftp://admin.example.com/', false],
            'leading whitespace' => [' https://evil.example/', false],
            'control character' => ["/admin/\n", false],
            'relative without slash' => ['admin/foo', false],
            'empty string' => ['', false],
            'not a string' => [['https://admin.example.com/'], false],
            'null' => [null, false],
        ];
    }
}
