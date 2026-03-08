<?php

namespace Makraz\EditorjsBundle\Tests\DTO\Tools;

use Makraz\EditorjsBundle\DTO\Tools\AlertTool;
use Makraz\EditorjsBundle\DTO\Tools\AlignmentHeaderTool;
use Makraz\EditorjsBundle\DTO\Tools\AlignmentParagraphTool;
use Makraz\EditorjsBundle\DTO\Tools\AttachesTool;
use Makraz\EditorjsBundle\DTO\Tools\ColumnsTool;
use Makraz\EditorjsBundle\DTO\Tools\CustomTool;
use Makraz\EditorjsBundle\DTO\Tools\HyperlinkTool;
use Makraz\EditorjsBundle\DTO\Tools\LinkAutocompleteTool;
use Makraz\EditorjsBundle\DTO\Tools\NestedListTool;
use Makraz\EditorjsBundle\DTO\Tools\SimpleImageTool;
use Makraz\EditorjsBundle\DTO\Tools\StrikethroughTool;
use Makraz\EditorjsBundle\DTO\Tools\TextColorTool;
use Makraz\EditorjsBundle\DTO\Tools\ToggleBlockTool;
use Makraz\EditorjsBundle\DTO\Tools\ToolInterface;
use PHPUnit\Framework\TestCase;

class CommunityToolsTest extends TestCase
{
    // --- AlertTool ---

    public function testAlertToolDefaults(): void
    {
        $tool = new AlertTool();

        $this->assertSame('alert', $tool->getName());
        $this->assertSame('editorjs-alert', $tool->getPackage());
        $this->assertSame([
            'defaultType' => 'info',
            'defaultAlign' => 'left',
            'messagePlaceholder' => 'Enter alert message',
        ], $tool->getConfig());
    }

    public function testAlertToolCustom(): void
    {
        $tool = new AlertTool(defaultType: 'warning', defaultAlign: 'center', messagePlaceholder: 'Alert!');

        $this->assertSame('warning', $tool->getConfig()['defaultType']);
        $this->assertSame('center', $tool->getConfig()['defaultAlign']);
        $this->assertSame('Alert!', $tool->getConfig()['messagePlaceholder']);
    }

    // --- AlignmentParagraphTool ---

    public function testAlignmentParagraphToolDefaults(): void
    {
        $tool = new AlignmentParagraphTool();

        $this->assertSame('paragraph', $tool->getName());
        $this->assertSame('editorjs-paragraph-with-alignment', $tool->getPackage());
        $this->assertSame([
            'placeholder' => '',
            'defaultAlignment' => 'left',
            'preserveBlank' => false,
        ], $tool->getConfig());
    }

    public function testAlignmentParagraphToolCustom(): void
    {
        $tool = new AlignmentParagraphTool(
            placeholder: 'Write...',
            defaultAlignment: 'center',
            preserveBlank: true,
        );

        $config = $tool->getConfig();
        $this->assertSame('Write...', $config['placeholder']);
        $this->assertSame('center', $config['defaultAlignment']);
        $this->assertTrue($config['preserveBlank']);
    }

    // --- AlignmentHeaderTool ---

    public function testAlignmentHeaderToolDefaults(): void
    {
        $tool = new AlignmentHeaderTool();

        $this->assertSame('header', $tool->getName());
        $this->assertSame('editorjs-header-with-alignment', $tool->getPackage());
        $this->assertSame([
            'placeholder' => 'Enter a header',
            'levels' => [1, 2, 3, 4, 5, 6],
            'defaultLevel' => 2,
            'defaultAlignment' => 'left',
        ], $tool->getConfig());
    }

    public function testAlignmentHeaderToolCustom(): void
    {
        $tool = new AlignmentHeaderTool(
            placeholder: 'Title',
            levels: [1, 2],
            defaultLevel: 1,
            defaultAlignment: 'right',
        );

        $config = $tool->getConfig();
        $this->assertSame('Title', $config['placeholder']);
        $this->assertSame([1, 2], $config['levels']);
        $this->assertSame(1, $config['defaultLevel']);
        $this->assertSame('right', $config['defaultAlignment']);
    }

    // --- AttachesTool ---

    public function testAttachesToolDefaults(): void
    {
        $tool = new AttachesTool();

        $this->assertSame('attaches', $tool->getName());
        $this->assertSame('@editorjs/attaches', $tool->getPackage());
        $this->assertSame([
            'field' => 'file',
            'buttonText' => 'Select file',
        ], $tool->getConfig());
    }

    public function testAttachesToolWithAllOptions(): void
    {
        $tool = new AttachesTool(
            endpoint: '/upload',
            field: 'attachment',
            buttonText: 'Upload',
            types: 'application/pdf',
            errorMessage: 'Failed',
        );

        $config = $tool->getConfig();
        $this->assertSame('/upload', $config['endpoint']);
        $this->assertSame('attachment', $config['field']);
        $this->assertSame('Upload', $config['buttonText']);
        $this->assertSame('application/pdf', $config['types']);
        $this->assertSame('Failed', $config['errorMessage']);
    }

    public function testAttachesToolOptionalFieldsOmittedWhenNull(): void
    {
        $tool = new AttachesTool();
        $config = $tool->getConfig();

        $this->assertArrayNotHasKey('endpoint', $config);
        $this->assertArrayNotHasKey('types', $config);
        $this->assertArrayNotHasKey('errorMessage', $config);
    }

    // --- NestedListTool ---

    public function testNestedListToolDefaults(): void
    {
        $tool = new NestedListTool();

        $this->assertSame('list', $tool->getName());
        $this->assertSame('@editorjs/nested-list', $tool->getPackage());
        $this->assertSame(['defaultStyle' => 'unordered'], $tool->getConfig());
    }

    public function testNestedListToolOrdered(): void
    {
        $tool = new NestedListTool(defaultStyle: 'ordered');
        $this->assertSame('ordered', $tool->getConfig()['defaultStyle']);
    }

    // --- HyperlinkTool ---

    public function testHyperlinkToolDefaults(): void
    {
        $tool = new HyperlinkTool();

        $this->assertSame('hyperlink', $tool->getName());
        $this->assertSame('editorjs-hyperlink', $tool->getPackage());
        $this->assertSame(['shortcut' => 'CMD+K'], $tool->getConfig());
    }

    public function testHyperlinkToolWithAllOptions(): void
    {
        $tool = new HyperlinkTool(
            shortcut: 'CMD+L',
            target: '_blank',
            rel: 'nofollow',
            availableTargets: ['_blank', '_self'],
            availableRels: ['nofollow', 'noreferrer'],
        );

        $config = $tool->getConfig();
        $this->assertSame('CMD+L', $config['shortcut']);
        $this->assertSame('_blank', $config['target']);
        $this->assertSame('nofollow', $config['rel']);
        $this->assertSame(['_blank', '_self'], $config['availableTargets']);
        $this->assertSame(['nofollow', 'noreferrer'], $config['availableRels']);
    }

    public function testHyperlinkToolOmitsNullOptions(): void
    {
        $tool = new HyperlinkTool();
        $config = $tool->getConfig();

        $this->assertArrayNotHasKey('target', $config);
        $this->assertArrayNotHasKey('rel', $config);
        $this->assertArrayNotHasKey('availableTargets', $config);
        $this->assertArrayNotHasKey('availableRels', $config);
    }

    // --- TextColorTool ---

    public function testTextColorToolTextMode(): void
    {
        $tool = new TextColorTool();

        $this->assertSame('textColor', $tool->getName());
        $this->assertSame('editorjs-text-color-plugin', $tool->getPackage());
        $this->assertSame([
            'defaultColor' => '#FF1300',
            'type' => 'text',
        ], $tool->getConfig());
    }

    public function testTextColorToolMarkerMode(): void
    {
        $tool = new TextColorTool(type: 'marker');

        $this->assertSame('colorMarker', $tool->getName());
        $this->assertSame('marker', $tool->getConfig()['type']);
    }

    public function testTextColorToolCustomColor(): void
    {
        $tool = new TextColorTool(defaultColor: '#00FF00');
        $this->assertSame('#00FF00', $tool->getConfig()['defaultColor']);
    }

    // --- ToggleBlockTool ---

    public function testToggleBlockToolDefaults(): void
    {
        $tool = new ToggleBlockTool();

        $this->assertSame('toggle', $tool->getName());
        $this->assertSame('editorjs-toggle-block', $tool->getPackage());
        $this->assertSame(['placeholder' => 'Toggle title'], $tool->getConfig());
    }

    public function testToggleBlockToolCustom(): void
    {
        $tool = new ToggleBlockTool(placeholder: 'Click to expand');
        $this->assertSame('Click to expand', $tool->getConfig()['placeholder']);
    }

    // --- SimpleImageTool ---

    public function testSimpleImageTool(): void
    {
        $tool = new SimpleImageTool();

        $this->assertSame('simpleImage', $tool->getName());
        $this->assertSame('@editorjs/simple-image', $tool->getPackage());
        $this->assertSame([], $tool->getConfig());
    }

    // --- StrikethroughTool ---

    public function testStrikethroughTool(): void
    {
        $tool = new StrikethroughTool();

        $this->assertSame('strikethrough', $tool->getName());
        $this->assertSame('@sotaproject/strikethrough', $tool->getPackage());
        $this->assertSame([], $tool->getConfig());
    }

    // --- LinkAutocompleteTool ---

    public function testLinkAutocompleteToolDefaults(): void
    {
        $tool = new LinkAutocompleteTool();

        $this->assertSame('linkAutocomplete', $tool->getName());
        $this->assertSame('@editorjs/link-autocomplete', $tool->getPackage());
        $this->assertSame([], $tool->getConfig());
    }

    public function testLinkAutocompleteToolWithEndpoint(): void
    {
        $tool = new LinkAutocompleteTool(endpoint: '/api/search', queryParam: 'q');

        $config = $tool->getConfig();
        $this->assertSame('/api/search', $config['endpoint']);
        $this->assertSame('q', $config['queryParam']);
    }

    // --- ColumnsTool ---

    public function testColumnsToolDefaults(): void
    {
        $tool = new ColumnsTool();

        $this->assertSame('columns', $tool->getName());
        $this->assertSame('@calumk/editorjs-columns', $tool->getPackage());

        $config = $tool->getConfig();
        $this->assertTrue($config['requireEditorJS']);
        $this->assertArrayNotHasKey('tools', $config);
    }

    public function testColumnsToolWithCustomTools(): void
    {
        $tool = new ColumnsTool(tools: ['header' => [], 'paragraph' => []]);

        $config = $tool->getConfig();
        $this->assertSame(['header' => [], 'paragraph' => []], $config['tools']);
    }

    // --- CustomTool ---

    public function testCustomToolWithConfig(): void
    {
        $tool = new CustomTool(
            name: 'myTool',
            package: 'my-editorjs-tool',
            config: ['option1' => 'value1'],
        );

        $this->assertSame('myTool', $tool->getName());
        $this->assertSame('my-editorjs-tool', $tool->getPackage());
        $this->assertSame(['option1' => 'value1'], $tool->getConfig());
    }

    public function testCustomToolWithoutConfig(): void
    {
        $tool = new CustomTool(name: 'undo', package: 'editorjs-undo');

        $this->assertSame('undo', $tool->getName());
        $this->assertSame('editorjs-undo', $tool->getPackage());
        $this->assertSame([], $tool->getConfig());
    }

    // --- All community tools implement ToolInterface ---

    public function testAllCommunityToolsImplementInterface(): void
    {
        $tools = [
            new AlertTool(),
            new AlignmentParagraphTool(),
            new AlignmentHeaderTool(),
            new AttachesTool(),
            new NestedListTool(),
            new HyperlinkTool(),
            new TextColorTool(),
            new ToggleBlockTool(),
            new SimpleImageTool(),
            new StrikethroughTool(),
            new LinkAutocompleteTool(),
            new ColumnsTool(),
            new CustomTool(name: 'test', package: 'test'),
        ];

        foreach ($tools as $tool) {
            $this->assertInstanceOf(ToolInterface::class, $tool, $tool::class);
        }
    }

    // --- All community tools have a non-null package ---

    public function testAllCommunityToolsHavePackage(): void
    {
        $tools = [
            new AlertTool(),
            new AlignmentParagraphTool(),
            new AlignmentHeaderTool(),
            new AttachesTool(),
            new NestedListTool(),
            new HyperlinkTool(),
            new TextColorTool(),
            new ToggleBlockTool(),
            new SimpleImageTool(),
            new StrikethroughTool(),
            new LinkAutocompleteTool(),
            new ColumnsTool(),
        ];

        foreach ($tools as $tool) {
            $this->assertNotNull($tool->getPackage(), \sprintf('%s::getPackage() should not be null', $tool::class));
            $this->assertNotEmpty($tool->getPackage(), \sprintf('%s::getPackage() should not be empty', $tool::class));
        }
    }
}
