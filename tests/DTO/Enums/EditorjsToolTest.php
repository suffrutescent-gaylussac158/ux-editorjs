<?php

namespace Makraz\EditorjsBundle\Tests\DTO\Enums;

use Makraz\EditorjsBundle\DTO\Enums\EditorjsTool;
use PHPUnit\Framework\TestCase;

class EditorjsToolTest extends TestCase
{
    public function testAllCasesExist(): void
    {
        $cases = EditorjsTool::cases();

        $this->assertCount(16, $cases);
    }

    /**
     * @dataProvider toolValuesProvider
     */
    public function testToolValues(EditorjsTool $tool, string $expectedValue): void
    {
        $this->assertSame($expectedValue, $tool->value);
    }

    public static function toolValuesProvider(): array
    {
        return [
            [EditorjsTool::HEADER, 'header'],
            [EditorjsTool::LIST, 'list'],
            [EditorjsTool::PARAGRAPH, 'paragraph'],
            [EditorjsTool::IMAGE, 'image'],
            [EditorjsTool::CODE, 'code'],
            [EditorjsTool::DELIMITER, 'delimiter'],
            [EditorjsTool::QUOTE, 'quote'],
            [EditorjsTool::WARNING, 'warning'],
            [EditorjsTool::TABLE, 'table'],
            [EditorjsTool::EMBED, 'embed'],
            [EditorjsTool::MARKER, 'marker'],
            [EditorjsTool::INLINE_CODE, 'inlineCode'],
            [EditorjsTool::CHECKLIST, 'checklist'],
            [EditorjsTool::LINK, 'linkTool'],
            [EditorjsTool::RAW, 'raw'],
            [EditorjsTool::UNDERLINE, 'underline'],
        ];
    }

    public function testFromValidValue(): void
    {
        $tool = EditorjsTool::from('header');
        $this->assertSame(EditorjsTool::HEADER, $tool);
    }

    public function testTryFromInvalidValue(): void
    {
        $this->assertNull(EditorjsTool::tryFrom('nonexistent'));
    }

    public function testFromInvalidValueThrows(): void
    {
        $this->expectException(\ValueError::class);
        EditorjsTool::from('nonexistent');
    }
}
