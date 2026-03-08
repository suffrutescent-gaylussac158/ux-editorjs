<?php

namespace Makraz\EditorjsBundle\Tests\DTO\Tools;

use Makraz\EditorjsBundle\DTO\Tools\AlignmentBlockTune;
use Makraz\EditorjsBundle\DTO\Tools\ImageCropResizeTune;
use Makraz\EditorjsBundle\DTO\Tools\IndentTune;
use Makraz\EditorjsBundle\DTO\Tools\PerToolTuneInterface;
use Makraz\EditorjsBundle\DTO\Tools\TextVariantTune;
use Makraz\EditorjsBundle\DTO\Tools\ToolInterface;
use Makraz\EditorjsBundle\DTO\Tools\TuneInterface;
use PHPUnit\Framework\TestCase;

class TunesTest extends TestCase
{
    // --- AlignmentBlockTune ---

    public function testAlignmentBlockTuneDefaults(): void
    {
        $tune = new AlignmentBlockTune();

        $this->assertSame('textAlignment', $tune->getName());
        $this->assertSame('editorjs-alignment-blocktune', $tune->getPackage());
        $this->assertSame(['default' => 'left'], $tune->getConfig());
    }

    public function testAlignmentBlockTuneCustom(): void
    {
        $tune = new AlignmentBlockTune(default: 'center');
        $this->assertSame('center', $tune->getConfig()['default']);
    }

    public function testAlignmentBlockTuneImplementsTuneInterface(): void
    {
        $tune = new AlignmentBlockTune();
        $this->assertInstanceOf(TuneInterface::class, $tune);
        $this->assertInstanceOf(ToolInterface::class, $tune);
        $this->assertNotInstanceOf(PerToolTuneInterface::class, $tune);
    }

    // --- TextVariantTune ---

    public function testTextVariantTune(): void
    {
        $tune = new TextVariantTune();

        $this->assertSame('textVariant', $tune->getName());
        $this->assertSame('@editorjs/text-variant-tune', $tune->getPackage());
        $this->assertSame([], $tune->getConfig());
    }

    public function testTextVariantTuneImplementsTuneInterface(): void
    {
        $this->assertInstanceOf(TuneInterface::class, new TextVariantTune());
        $this->assertNotInstanceOf(PerToolTuneInterface::class, new TextVariantTune());
    }

    // --- IndentTune ---

    public function testIndentTuneDefaults(): void
    {
        $tune = new IndentTune();

        $this->assertSame('indentTune', $tune->getName());
        $this->assertSame('editorjs-indent-tune', $tune->getPackage());
        $this->assertSame([
            'maxIndent' => 5,
            'indentSize' => 24,
            'direction' => 'ltr',
        ], $tune->getConfig());
    }

    public function testIndentTuneCustom(): void
    {
        $tune = new IndentTune(maxIndent: 10, indentSize: 48, direction: 'rtl');

        $config = $tune->getConfig();
        $this->assertSame(10, $config['maxIndent']);
        $this->assertSame(48, $config['indentSize']);
        $this->assertSame('rtl', $config['direction']);
    }

    public function testIndentTuneImplementsTuneInterface(): void
    {
        $this->assertInstanceOf(TuneInterface::class, new IndentTune());
        $this->assertNotInstanceOf(PerToolTuneInterface::class, new IndentTune());
    }

    // --- ImageCropResizeTune ---

    public function testImageCropResizeTune(): void
    {
        $tune = new ImageCropResizeTune();

        $this->assertSame('CropperTune', $tune->getName());
        $this->assertSame('editorjs-image-crop-resize', $tune->getPackage());
        $this->assertSame([], $tune->getConfig());
    }

    public function testImageCropResizeTuneImplementsPerToolTuneInterface(): void
    {
        $tune = new ImageCropResizeTune();
        $this->assertInstanceOf(PerToolTuneInterface::class, $tune);
        $this->assertInstanceOf(TuneInterface::class, $tune);
        $this->assertInstanceOf(ToolInterface::class, $tune);
    }

    public function testImageCropResizeTuneApplicableTools(): void
    {
        $tune = new ImageCropResizeTune();
        $this->assertSame(['image'], $tune->getApplicableTools());
    }

    // --- Global vs per-tool distinction ---

    public function testGlobalTunesAreNotPerToolTunes(): void
    {
        $globalTunes = [
            new AlignmentBlockTune(),
            new TextVariantTune(),
            new IndentTune(),
        ];

        foreach ($globalTunes as $tune) {
            $this->assertInstanceOf(TuneInterface::class, $tune);
            $this->assertNotInstanceOf(PerToolTuneInterface::class, $tune, \sprintf(
                '%s should NOT implement PerToolTuneInterface',
                $tune::class,
            ));
        }
    }

    public function testPerToolTunesImplementPerToolInterface(): void
    {
        $perToolTunes = [
            new ImageCropResizeTune(),
        ];

        foreach ($perToolTunes as $tune) {
            $this->assertInstanceOf(PerToolTuneInterface::class, $tune);
        }
    }
}
