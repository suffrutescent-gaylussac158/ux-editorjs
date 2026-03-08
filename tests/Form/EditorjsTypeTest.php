<?php

namespace Makraz\EditorjsBundle\Tests\Form;

use Makraz\EditorjsBundle\DTO\Enums\EditorjsTool;
use Makraz\EditorjsBundle\DTO\Tools\AlignmentBlockTune;
use Makraz\EditorjsBundle\DTO\Tools\CustomTool;
use Makraz\EditorjsBundle\DTO\Tools\HeaderTool;
use Makraz\EditorjsBundle\DTO\Tools\IndentTune;
use Makraz\EditorjsBundle\DTO\Tools\TextVariantTune;
use Makraz\EditorjsBundle\Form\EditorjsType;
use Symfony\Component\Form\Test\TypeTestCase;

class EditorjsTypeTest extends TypeTestCase
{
    public function testSubmitValidData(): void
    {
        $jsonData = json_encode([
            'time' => 1234567890,
            'blocks' => [
                ['type' => 'paragraph', 'data' => ['text' => 'Hello World']],
            ],
            'version' => '2.30.0',
        ]);

        $form = $this->factory->create(EditorjsType::class);
        $form->submit($jsonData);

        $this->assertTrue($form->isSynchronized());
        $this->assertSame($jsonData, $form->getData());
    }

    public function testDefaultTools(): void
    {
        $form = $this->factory->create(EditorjsType::class);
        $view = $form->createView();

        $tools = json_decode($view->vars['attr']['data-tools'], true);

        $this->assertArrayHasKey('header', $tools);
        $this->assertArrayHasKey('list', $tools);
        $this->assertArrayHasKey('paragraph', $tools);
    }

    public function testCustomToolsWithEnum(): void
    {
        $form = $this->factory->create(EditorjsType::class, null, [
            'editorjs_tools' => [
                EditorjsTool::CODE,
                EditorjsTool::QUOTE,
            ],
        ]);
        $view = $form->createView();

        $tools = json_decode($view->vars['attr']['data-tools'], true);

        $this->assertArrayHasKey('code', $tools);
        $this->assertArrayHasKey('quote', $tools);
        $this->assertArrayNotHasKey('header', $tools);
    }

    public function testCustomToolsWithDto(): void
    {
        $form = $this->factory->create(EditorjsType::class, null, [
            'editorjs_tools' => [
                new HeaderTool(levels: [1, 2, 3], defaultLevel: 2),
            ],
        ]);
        $view = $form->createView();

        $tools = json_decode($view->vars['attr']['data-tools'], true);

        $this->assertArrayHasKey('header', $tools);
        $this->assertSame([1, 2, 3], $tools['header']['config']['levels']);
        $this->assertSame(2, $tools['header']['config']['defaultLevel']);
        $this->assertArrayNotHasKey('package', $tools['header']);
    }

    public function testCustomToolWithPackage(): void
    {
        $form = $this->factory->create(EditorjsType::class, null, [
            'editorjs_tools' => [
                new CustomTool(
                    name: 'paragraph',
                    package: 'editorjs-paragraph-with-alignment',
                    config: ['defaultAlignment' => 'left'],
                ),
            ],
        ]);
        $view = $form->createView();

        $tools = json_decode($view->vars['attr']['data-tools'], true);

        $this->assertArrayHasKey('paragraph', $tools);
        $this->assertSame('editorjs-paragraph-with-alignment', $tools['paragraph']['package']);
        $this->assertSame('left', $tools['paragraph']['config']['defaultAlignment']);
    }

    public function testCustomToolWithoutConfig(): void
    {
        $form = $this->factory->create(EditorjsType::class, null, [
            'editorjs_tools' => [
                new CustomTool(name: 'undo', package: 'editorjs-undo'),
            ],
        ]);
        $view = $form->createView();

        $tools = json_decode($view->vars['attr']['data-tools'], true);

        $this->assertArrayHasKey('undo', $tools);
        $this->assertSame('editorjs-undo', $tools['undo']['package']);
        $this->assertArrayNotHasKey('config', $tools['undo']);
    }

    public function testMixedBuiltinAndCustomTools(): void
    {
        $form = $this->factory->create(EditorjsType::class, null, [
            'editorjs_tools' => [
                new CustomTool(
                    name: 'paragraph',
                    package: 'editorjs-paragraph-with-alignment',
                ),
                new HeaderTool(levels: [1, 2, 3]),
                EditorjsTool::CODE,
            ],
        ]);
        $view = $form->createView();

        $tools = json_decode($view->vars['attr']['data-tools'], true);

        $this->assertCount(3, $tools);
        $this->assertArrayHasKey('package', $tools['paragraph']);
        $this->assertArrayNotHasKey('package', $tools['header']);
        $this->assertSame([], $tools['code']);
    }

    public function testExtraOptions(): void
    {
        $form = $this->factory->create(EditorjsType::class, null, [
            'editorjs_options' => [
                'placeholder' => 'Custom placeholder',
                'minHeight' => 400,
                'autofocus' => true,
            ],
        ]);
        $view = $form->createView();

        $options = json_decode($view->vars['attr']['data-extra-options'], true);

        $this->assertSame('Custom placeholder', $options['placeholder']);
        $this->assertSame(400, $options['minHeight']);
        $this->assertTrue($options['autofocus']);
    }

    public function testMaxWidthWithPixels(): void
    {
        $form = $this->factory->create(EditorjsType::class, null, [
            'editorjs_options' => [
                'maxWidth' => 900,
            ],
        ]);
        $view = $form->createView();

        $options = json_decode($view->vars['attr']['data-extra-options'], true);

        $this->assertSame(900, $options['maxWidth']);
    }

    public function testMaxWidthWithPercentage(): void
    {
        $form = $this->factory->create(EditorjsType::class, null, [
            'editorjs_options' => [
                'maxWidth' => '80%',
            ],
        ]);
        $view = $form->createView();

        $options = json_decode($view->vars['attr']['data-extra-options'], true);

        $this->assertSame('80%', $options['maxWidth']);
    }

    public function testMaxWidthDefaultValue(): void
    {
        $form = $this->factory->create(EditorjsType::class);
        $view = $form->createView();

        $options = json_decode($view->vars['attr']['data-extra-options'], true);

        $this->assertSame(650, $options['maxWidth']);
    }

    public function testMinHeightWithPercentage(): void
    {
        $form = $this->factory->create(EditorjsType::class, null, [
            'editorjs_options' => [
                'minHeight' => '50%',
            ],
        ]);
        $view = $form->createView();

        $options = json_decode($view->vars['attr']['data-extra-options'], true);

        $this->assertSame('50%', $options['minHeight']);
    }

    public function testBorderEnabled(): void
    {
        $form = $this->factory->create(EditorjsType::class, null, [
            'editorjs_options' => [
                'border' => true,
            ],
        ]);
        $view = $form->createView();

        $options = json_decode($view->vars['attr']['data-extra-options'], true);

        $this->assertTrue($options['border']);
    }

    public function testBorderCustomValue(): void
    {
        $form = $this->factory->create(EditorjsType::class, null, [
            'editorjs_options' => [
                'border' => '2px dashed #ccc',
            ],
        ]);
        $view = $form->createView();

        $options = json_decode($view->vars['attr']['data-extra-options'], true);

        $this->assertSame('2px dashed #ccc', $options['border']);
    }

    public function testBorderDefaultDisabled(): void
    {
        $form = $this->factory->create(EditorjsType::class);
        $view = $form->createView();

        $options = json_decode($view->vars['attr']['data-extra-options'], true);

        $this->assertFalse($options['border']);
    }

    public function testBlockPrefix(): void
    {
        $form = $this->factory->create(EditorjsType::class);

        $this->assertContains('editorjs', $form->createView()->vars['block_prefixes']);
    }

    public function testTunesAreResolvedSeparately(): void
    {
        $form = $this->factory->create(EditorjsType::class, null, [
            'editorjs_tools' => [
                EditorjsTool::HEADER,
                EditorjsTool::PARAGRAPH,
                new AlignmentBlockTune(default: 'center'),
            ],
        ]);
        $view = $form->createView();

        $tools = json_decode($view->vars['attr']['data-tools'], true);
        $tunes = json_decode($view->vars['attr']['data-tunes'], true);

        // Tune is also registered as a tool
        $this->assertArrayHasKey('textAlignment', $tools);
        $this->assertSame('editorjs-alignment-blocktune', $tools['textAlignment']['package']);
        $this->assertSame('center', $tools['textAlignment']['config']['default']);

        // Tune name appears in the tunes array
        $this->assertContains('textAlignment', $tunes);
    }

    public function testMultipleTunes(): void
    {
        $form = $this->factory->create(EditorjsType::class, null, [
            'editorjs_tools' => [
                EditorjsTool::HEADER,
                new AlignmentBlockTune(),
                new TextVariantTune(),
                new IndentTune(maxIndent: 3),
            ],
        ]);
        $view = $form->createView();

        $tunes = json_decode($view->vars['attr']['data-tunes'], true);

        $this->assertCount(3, $tunes);
        $this->assertContains('textAlignment', $tunes);
        $this->assertContains('textVariant', $tunes);
        $this->assertContains('indentTune', $tunes);
    }

    public function testNoTunesWhenNoneProvided(): void
    {
        $form = $this->factory->create(EditorjsType::class, null, [
            'editorjs_tools' => [
                EditorjsTool::HEADER,
                EditorjsTool::PARAGRAPH,
            ],
        ]);
        $view = $form->createView();

        $tunes = json_decode($view->vars['attr']['data-tunes'], true);

        $this->assertSame([], $tunes);
    }

    public function testTuneWithConfig(): void
    {
        $form = $this->factory->create(EditorjsType::class, null, [
            'editorjs_tools' => [
                new IndentTune(maxIndent: 10, indentSize: 48, direction: 'rtl'),
            ],
        ]);
        $view = $form->createView();

        $tools = json_decode($view->vars['attr']['data-tools'], true);

        $this->assertSame(10, $tools['indentTune']['config']['maxIndent']);
        $this->assertSame(48, $tools['indentTune']['config']['indentSize']);
        $this->assertSame('rtl', $tools['indentTune']['config']['direction']);
        $this->assertSame('editorjs-indent-tune', $tools['indentTune']['package']);
    }

    public function testPerToolTuneAppliedToTargetTool(): void
    {
        $form = $this->factory->create(EditorjsType::class, null, [
            'editorjs_tools' => [
                EditorjsTool::IMAGE,
                new \Makraz\EditorjsBundle\DTO\Tools\ImageCropResizeTune(),
            ],
        ]);
        $view = $form->createView();

        $tools = json_decode($view->vars['attr']['data-tools'], true);
        $tunes = json_decode($view->vars['attr']['data-tunes'], true);

        // PerToolTune should be registered as a tool
        $this->assertArrayHasKey('CropperTune', $tools);
        $this->assertSame('editorjs-image-crop-resize', $tools['CropperTune']['package']);

        // PerToolTune should apply its name to the target tool's tunes array
        $this->assertArrayHasKey('tunes', $tools['image']);
        $this->assertContains('CropperTune', $tools['image']['tunes']);

        // PerToolTune should NOT appear in global tunes list
        $this->assertNotContains('CropperTune', $tunes);
    }

    public function testPerToolTuneNotAppliedWhenTargetToolMissing(): void
    {
        $form = $this->factory->create(EditorjsType::class, null, [
            'editorjs_tools' => [
                EditorjsTool::HEADER,
                new \Makraz\EditorjsBundle\DTO\Tools\ImageCropResizeTune(),
            ],
        ]);
        $view = $form->createView();

        $tools = json_decode($view->vars['attr']['data-tools'], true);

        // ImageCropResizeTune targets 'image', which is not present
        $this->assertArrayNotHasKey('image', $tools);
        $this->assertArrayNotHasKey('tunes', $tools['header'] ?? []);
    }

    public function testStringToolsResolvedAsEmpty(): void
    {
        $form = $this->factory->create(EditorjsType::class, null, [
            'editorjs_tools' => ['customTool', 'anotherTool'],
        ]);
        $view = $form->createView();

        $tools = json_decode($view->vars['attr']['data-tools'], true);

        $this->assertArrayHasKey('customTool', $tools);
        $this->assertSame([], $tools['customTool']);
        $this->assertArrayHasKey('anotherTool', $tools);
        $this->assertSame([], $tools['anotherTool']);
    }

    public function testEmptyToolsList(): void
    {
        $form = $this->factory->create(EditorjsType::class, null, [
            'editorjs_tools' => [],
        ]);
        $view = $form->createView();

        $tools = json_decode($view->vars['attr']['data-tools'], true);
        $tunes = json_decode($view->vars['attr']['data-tunes'], true);

        $this->assertSame([], $tools);
        $this->assertSame([], $tunes);
    }

    public function testExtraOptionsDefaults(): void
    {
        $form = $this->factory->create(EditorjsType::class);
        $view = $form->createView();

        $options = json_decode($view->vars['attr']['data-extra-options'], true);

        $this->assertSame('Start writing...', $options['placeholder']);
        $this->assertSame(200, $options['minHeight']);
        $this->assertFalse($options['readOnly']);
        $this->assertFalse($options['autofocus']);
        $this->assertTrue($options['inlineToolbar']);
        $this->assertSame(650, $options['maxWidth']);
        $this->assertFalse($options['border']);
    }
}
