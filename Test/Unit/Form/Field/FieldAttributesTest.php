<?php declare(strict_types=1);

namespace Loki\AdminComponents\Test\Unit\Form\Field;

use Loki\AdminComponents\Form\Field\Field;
use PHPUnit\Framework\TestCase;

class FieldAttributesTest extends TestCase
{
    public function testInvalidAttributeNamesAreRemoved(): void
    {
        $field = new Field(null, ['field_attributes' => [
            'data-foo' => 'a',
            'x-on:click' => 'b',
            '@change' => 'c',
            ':value' => 'd',
            'maxlength' => 10,
            '"><img src=x onerror=alert(1)' => 'e',
            'foo bar' => 'f',
            0 => 'g',
        ]]);

        $this->assertSame(
            ['data-foo' => 'a', 'x-on:click' => 'b', '@change' => 'c', ':value' => 'd', 'maxlength' => 10],
            $field->getFieldAttributes()
        );
    }
}
