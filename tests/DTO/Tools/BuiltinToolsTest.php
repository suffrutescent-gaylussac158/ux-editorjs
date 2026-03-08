<?php

namespace Makraz\EditorjsBundle\Tests\DTO\Tools;

use Makraz\EditorjsBundle\DTO\Tools\AbstractTool;
use Makraz\EditorjsBundle\DTO\Tools\ChecklistTool;
use Makraz\EditorjsBundle\DTO\Tools\CodeTool;
use Makraz\EditorjsBundle\DTO\Tools\DelimiterTool;
use Makraz\EditorjsBundle\DTO\Tools\EmbedTool;
use Makraz\EditorjsBundle\DTO\Tools\HeaderTool;
use Makraz\EditorjsBundle\DTO\Tools\ImageTool;
use Makraz\EditorjsBundle\DTO\Tools\InlineCodeTool;
use Makraz\EditorjsBundle\DTO\Tools\LinkTool;
use Makraz\EditorjsBundle\DTO\Tools\ListTool;
use Makraz\EditorjsBundle\DTO\Tools\MarkerTool;
use Makraz\EditorjsBundle\DTO\Tools\ParagraphTool;
use Makraz\EditorjsBundle\DTO\Tools\QuoteTool;
use Makraz\EditorjsBundle\DTO\Tools\RawTool;
use Makraz\EditorjsBundle\DTO\Tools\TableTool;
use Makraz\EditorjsBundle\DTO\Tools\ToolInterface;
use Makraz\EditorjsBundle\DTO\Tools\UnderlineTool;
use Makraz\EditorjsBundle\DTO\Tools\WarningTool;
use PHPUnit\Framework\TestCase;

class BuiltinToolsTest extends TestCase
{
    // --- HeaderTool ---

    public function testHeaderToolDefaults(): void
    {
        $tool = new HeaderTool();

        $this->assertSame('header', $tool->getName());
        $this->assertNull($tool->getPackage());
        $this->assertSame([
            'placeholder' => 'Enter a header',
            'levels' => [1, 2, 3, 4, 5, 6],
            'defaultLevel' => 2,
        ], $tool->getConfig());
    }

    public function testHeaderToolCustom(): void
    {
        $tool = new HeaderTool(placeholder: 'Title', levels: [1, 2], defaultLevel: 1);

        $this->assertSame('Title', $tool->getConfig()['placeholder']);
        $this->assertSame([1, 2], $tool->getConfig()['levels']);
        $this->assertSame(1, $tool->getConfig()['defaultLevel']);
    }

    public function testHeaderToolImplementsInterface(): void
    {
        $this->assertInstanceOf(ToolInterface::class, new HeaderTool());
        $this->assertInstanceOf(AbstractTool::class, new HeaderTool());
    }

    // --- ListTool ---

    public function testListToolDefaults(): void
    {
        $tool = new ListTool();

        $this->assertSame('list', $tool->getName());
        $this->assertNull($tool->getPackage());
        $this->assertSame([
            'defaultStyle' => 'unordered',
            'maxLevel' => 3,
        ], $tool->getConfig());
    }

    public function testListToolCustom(): void
    {
        $tool = new ListTool(defaultStyle: 'ordered', maxLevel: 5);

        $this->assertSame('ordered', $tool->getConfig()['defaultStyle']);
        $this->assertSame(5, $tool->getConfig()['maxLevel']);
    }

    // --- ParagraphTool ---

    public function testParagraphToolDefaults(): void
    {
        $tool = new ParagraphTool();

        $this->assertSame('paragraph', $tool->getName());
        $this->assertNull($tool->getPackage());
        $this->assertSame([
            'placeholder' => '',
            'preserveBlank' => false,
        ], $tool->getConfig());
    }

    public function testParagraphToolCustom(): void
    {
        $tool = new ParagraphTool(placeholder: 'Write here...', preserveBlank: true);

        $this->assertSame('Write here...', $tool->getConfig()['placeholder']);
        $this->assertTrue($tool->getConfig()['preserveBlank']);
    }

    // --- ImageTool ---

    public function testImageToolDefaults(): void
    {
        $tool = new ImageTool();

        $this->assertSame('image', $tool->getName());
        $this->assertNull($tool->getPackage());

        $config = $tool->getConfig();
        $this->assertSame(['withBorder' => false, 'stretched' => false, 'withBackground' => false], $config['actions']);
        $this->assertArrayNotHasKey('endpoints', $config);
    }

    public function testImageToolWithEndpoints(): void
    {
        $tool = new ImageTool(
            uploadEndpoint: '/upload/file',
            uploadByUrlEndpoint: '/upload/url',
            withBorder: true,
            stretched: true,
            withBackground: true,
        );

        $config = $tool->getConfig();
        $this->assertSame('/upload/file', $config['endpoints']['byFile']);
        $this->assertSame('/upload/url', $config['endpoints']['byUrl']);
        $this->assertTrue($config['actions']['withBorder']);
        $this->assertTrue($config['actions']['stretched']);
        $this->assertTrue($config['actions']['withBackground']);
    }

    public function testImageToolWithFileEndpointOnly(): void
    {
        $tool = new ImageTool(uploadEndpoint: '/upload/file');

        $config = $tool->getConfig();
        $this->assertSame('/upload/file', $config['endpoints']['byFile']);
        $this->assertArrayNotHasKey('byUrl', $config['endpoints']);
    }

    // --- CodeTool ---

    public function testCodeToolDefaults(): void
    {
        $tool = new CodeTool();

        $this->assertSame('code', $tool->getName());
        $this->assertNull($tool->getPackage());
        $this->assertSame(['placeholder' => 'Enter code'], $tool->getConfig());
    }

    public function testCodeToolCustom(): void
    {
        $tool = new CodeTool(placeholder: 'Paste code here');
        $this->assertSame('Paste code here', $tool->getConfig()['placeholder']);
    }

    // --- QuoteTool ---

    public function testQuoteToolDefaults(): void
    {
        $tool = new QuoteTool();

        $this->assertSame('quote', $tool->getName());
        $this->assertNull($tool->getPackage());
        $this->assertSame([
            'quotePlaceholder' => 'Enter a quote',
            'captionPlaceholder' => 'Quote\'s author',
        ], $tool->getConfig());
    }

    public function testQuoteToolCustom(): void
    {
        $tool = new QuoteTool(quotePlaceholder: 'Quote', captionPlaceholder: 'Author');
        $this->assertSame('Quote', $tool->getConfig()['quotePlaceholder']);
        $this->assertSame('Author', $tool->getConfig()['captionPlaceholder']);
    }

    // --- TableTool ---

    public function testTableToolDefaults(): void
    {
        $tool = new TableTool();

        $this->assertSame('table', $tool->getName());
        $this->assertNull($tool->getPackage());
        $this->assertSame([
            'rows' => 2,
            'cols' => 3,
            'withHeadings' => true,
        ], $tool->getConfig());
    }

    public function testTableToolCustom(): void
    {
        $tool = new TableTool(rows: 5, cols: 8, withHeadings: false);
        $this->assertSame(5, $tool->getConfig()['rows']);
        $this->assertSame(8, $tool->getConfig()['cols']);
        $this->assertFalse($tool->getConfig()['withHeadings']);
    }

    // --- WarningTool ---

    public function testWarningToolDefaults(): void
    {
        $tool = new WarningTool();

        $this->assertSame('warning', $tool->getName());
        $this->assertNull($tool->getPackage());
        $this->assertSame([
            'titlePlaceholder' => 'Title',
            'messagePlaceholder' => 'Message',
        ], $tool->getConfig());
    }

    public function testWarningToolCustom(): void
    {
        $tool = new WarningTool(titlePlaceholder: 'Warning!', messagePlaceholder: 'Details');
        $this->assertSame('Warning!', $tool->getConfig()['titlePlaceholder']);
        $this->assertSame('Details', $tool->getConfig()['messagePlaceholder']);
    }

    // --- EmbedTool ---

    public function testEmbedToolDefaults(): void
    {
        $tool = new EmbedTool();

        $this->assertSame('embed', $tool->getName());
        $this->assertNull($tool->getPackage());
        $this->assertSame([
            'services' => [
                'youtube' => true,
                'vimeo' => true,
                'codepen' => true,
                'github' => true,
            ],
        ], $tool->getConfig());
    }

    public function testEmbedToolCustomServices(): void
    {
        $tool = new EmbedTool(services: ['youtube', 'twitter']);
        $this->assertSame(['youtube' => true, 'twitter' => true], $tool->getConfig()['services']);
    }

    // --- LinkTool ---

    public function testLinkToolDefaults(): void
    {
        $tool = new LinkTool();

        $this->assertSame('linkTool', $tool->getName());
        $this->assertNull($tool->getPackage());
        $this->assertSame([], $tool->getConfig());
    }

    public function testLinkToolWithEndpoint(): void
    {
        $tool = new LinkTool(fetchEndpoint: '/api/link');
        $this->assertSame(['endpoint' => '/api/link'], $tool->getConfig());
    }

    // --- RawTool ---

    public function testRawToolDefaults(): void
    {
        $tool = new RawTool();

        $this->assertSame('raw', $tool->getName());
        $this->assertNull($tool->getPackage());
        $this->assertSame(['placeholder' => 'Enter raw HTML'], $tool->getConfig());
    }

    public function testRawToolCustom(): void
    {
        $tool = new RawTool(placeholder: 'HTML');
        $this->assertSame('HTML', $tool->getConfig()['placeholder']);
    }

    // --- Simple tools (no config) ---

    public function testDelimiterTool(): void
    {
        $tool = new DelimiterTool();
        $this->assertSame('delimiter', $tool->getName());
        $this->assertNull($tool->getPackage());
        $this->assertSame([], $tool->getConfig());
    }

    public function testMarkerTool(): void
    {
        $tool = new MarkerTool();
        $this->assertSame('marker', $tool->getName());
        $this->assertNull($tool->getPackage());
        $this->assertSame([], $tool->getConfig());
    }

    public function testInlineCodeTool(): void
    {
        $tool = new InlineCodeTool();
        $this->assertSame('inlineCode', $tool->getName());
        $this->assertNull($tool->getPackage());
        $this->assertSame([], $tool->getConfig());
    }

    public function testChecklistTool(): void
    {
        $tool = new ChecklistTool();
        $this->assertSame('checklist', $tool->getName());
        $this->assertNull($tool->getPackage());
        $this->assertSame([], $tool->getConfig());
    }

    public function testUnderlineTool(): void
    {
        $tool = new UnderlineTool();
        $this->assertSame('underline', $tool->getName());
        $this->assertNull($tool->getPackage());
        $this->assertSame([], $tool->getConfig());
    }

    // --- AbstractTool ---

    public function testAbstractToolPackageReturnsNull(): void
    {
        // All built-in tools extend AbstractTool which returns null for package
        $tools = [
            new HeaderTool(), new ListTool(), new ParagraphTool(),
            new CodeTool(), new QuoteTool(), new TableTool(),
            new ImageTool(), new RawTool(), new WarningTool(),
            new EmbedTool(), new LinkTool(), new DelimiterTool(),
            new MarkerTool(), new InlineCodeTool(), new ChecklistTool(),
            new UnderlineTool(),
        ];

        foreach ($tools as $tool) {
            $this->assertNull($tool->getPackage(), \sprintf('%s::getPackage() should return null', $tool::class));
        }
    }
}
