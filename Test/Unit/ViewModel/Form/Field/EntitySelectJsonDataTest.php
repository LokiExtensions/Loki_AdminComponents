<?php declare(strict_types=1);

namespace Loki\AdminComponents\Test\Unit\ViewModel\Form\Field;

use Loki\AdminComponents\ViewModel\Form\Field\EntitySelect;
use PHPUnit\Framework\TestCase;

class EntitySelectJsonDataTest extends TestCase
{
    public function testJsonDataCanNotBreakOutOfHtml(): void
    {
        $previewValue = '</data><img src=x onerror="alert(1)">\'&';
        $entitySelect = $this->createPartialMock(EntitySelect::class, ['getJsData']);
        $entitySelect->method('getJsData')->willReturn(['previewValue' => $previewValue]);

        $json = $entitySelect->getJsonData();

        $this->assertStringNotContainsString('<', $json);
        $this->assertStringNotContainsString('>', $json);
        $this->assertStringNotContainsString('"alert', $json);
        $this->assertStringNotContainsString('\'', $json);
        $this->assertStringNotContainsString('&', $json);
        $this->assertSame(['previewValue' => $previewValue], json_decode($json, true));
    }
}
