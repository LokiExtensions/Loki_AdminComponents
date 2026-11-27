<?php declare(strict_types=1);

namespace Loki\AdminComponents\Test\Unit\Filter;

use Loki\AdminComponents\Component\Form\FormRepository;
use Loki\AdminComponents\Component\Form\FormViewModel;
use Loki\AdminComponents\Filter\Security;
use Loki\AdminComponents\Form\Field\Field;
use Loki\AdminComponents\Form\Field\FieldType\Textarea;
use Loki\Components\Component\ComponentInterface;
use Loki\Components\Filter\FilterScope;
use Loki\Components\Filter\Security as CoreSecurity;
use PHPUnit\Framework\TestCase;

class SecurityTest extends TestCase
{
    public function testTagsAreStripped(): void
    {
        $this->assertSame('Hello', $this->getSecurity()->filter('<b>Hello</b>', new FilterScope()));
    }

    public function testEncodedTagsAreStrippedAfterDecoding(): void
    {
        $value = $this->getSecurity()->filter('&lt;img src=x onerror=alert(1)&gt;Label', new FilterScope());

        $this->assertSame('Label', $value);
        $this->assertStringNotContainsString('<', $value);
    }

    public function testEntitiesAreDecoded(): void
    {
        $this->assertSame('Tom & Jerry', $this->getSecurity()->filter('Tom &amp; Jerry', new FilterScope()));
    }

    public function testDoubleEncodedTagsAreStripped(): void
    {
        $value = $this->getSecurity()->filter('&amp;lt;script&amp;gt;alert(1)&amp;lt;/script&amp;gt;', new FilterScope());

        $this->assertStringNotContainsString('<', $value);
        $this->assertStringNotContainsString('&lt;', $value);
    }

    public function testJavascriptUrlsAreRemoved(): void
    {
        $this->assertStringNotContainsStringIgnoringCase(
            'javascript:',
            $this->getSecurity()->filter('javascript:alert(1)', new FilterScope())
        );
    }

    public function testNullBytesAreRemoved(): void
    {
        $this->assertSame('foobar', $this->getSecurity()->filter("foo\0bar", new FilterScope()));
    }

    public function testHtmlIsKeptForFieldsAllowingHtml(): void
    {
        $field = new Field(null, ['code' => 'content', 'allow_html' => true, 'field_type' => new Textarea()]);

        $viewModel = $this->createStub(FormViewModel::class);
        $viewModel->method('getFields')->willReturn(['content' => $field]);

        $component = $this->createStub(ComponentInterface::class);
        $component->method('getViewModel')->willReturn($viewModel);

        $repository = $this->createStub(FormRepository::class);
        $repository->method('getComponent')->willReturn($component);
        $component->method('getRepository')->willReturn($repository);

        $scope = new FilterScope();
        $scope->setComponent($component);
        $scope->setProperty('content');

        $this->assertSame('<p>Hello</p>', $this->getSecurity()->filter("<p>Hello</p>\0", $scope));

        $scope->setProperty('other');
        $this->assertSame('Hello', $this->getSecurity()->filter('<p>Hello</p>', $scope));
    }

    private function getSecurity(): Security
    {
        return new Security(new CoreSecurity());
    }
}
