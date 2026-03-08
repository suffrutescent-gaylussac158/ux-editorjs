<?php

namespace Makraz\EditorjsBundle\Tests;

use Makraz\EditorjsBundle\EditorjsBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class EditorjsBundleTest extends TestCase
{
    public function testBundleExtendsSymfonyBundle(): void
    {
        $bundle = new EditorjsBundle();
        $this->assertInstanceOf(Bundle::class, $bundle);
    }

    public function testGetPathReturnsProjectRoot(): void
    {
        $bundle = new EditorjsBundle();
        $expected = \dirname(__DIR__);

        $this->assertSame($expected, $bundle->getPath());
    }

    public function testGetPathContainsComposerJson(): void
    {
        $bundle = new EditorjsBundle();
        $this->assertFileExists($bundle->getPath().'/composer.json');
    }
}
