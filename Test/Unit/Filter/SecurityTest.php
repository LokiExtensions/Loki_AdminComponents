<?php declare(strict_types=1);

namespace Loki\AdminComponents\Test\Unit\Filter;

use Loki\AdminComponents\Filter\Security;
use Loki\Components\Filter\FilterScope;
use PHPUnit\Framework\TestCase;

class SecurityTest extends TestCase
{
    public function testTagsAreStripped(): void
    {
        $this->assertSame('Hello', (new Security())->filter('<b>Hello</b>', new FilterScope()));
    }

    public function testEncodedTagsAreStrippedAfterDecoding(): void
    {
        $value = (new Security())->filter('&lt;img src=x onerror=alert(1)&gt;Label', new FilterScope());

        $this->assertSame('Label', $value);
        $this->assertStringNotContainsString('<', $value);
    }

    public function testEntitiesAreDecoded(): void
    {
        $this->assertSame('Tom & Jerry', (new Security())->filter('Tom &amp; Jerry', new FilterScope()));
    }
}
