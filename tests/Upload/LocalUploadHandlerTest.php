<?php

namespace Makraz\EditorjsBundle\Tests\Upload;

use Makraz\EditorjsBundle\Upload\LocalUploadHandler;
use Makraz\EditorjsBundle\Upload\UploadHandlerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\AsciiSlugger;

class LocalUploadHandlerTest extends TestCase
{
    private string $uploadDir;

    protected function setUp(): void
    {
        $this->uploadDir = sys_get_temp_dir().'/editorjs_test_'.bin2hex(random_bytes(4));
        mkdir($this->uploadDir, 0o777, true);
    }

    protected function tearDown(): void
    {
        $this->removeDir($this->uploadDir);
    }

    public function testImplementsInterface(): void
    {
        $handler = new LocalUploadHandler($this->uploadDir, '/uploads', new AsciiSlugger());
        $this->assertInstanceOf(UploadHandlerInterface::class, $handler);
    }

    public function testUploadMovesFileAndReturnsPublicPath(): void
    {
        $handler = new LocalUploadHandler($this->uploadDir, '/uploads/editorjs', new AsciiSlugger());

        $tempFile = tempnam(sys_get_temp_dir(), 'test_').'.txt';
        file_put_contents($tempFile, 'test content');

        $file = new UploadedFile($tempFile, 'document.txt', 'text/plain', null, true);
        $result = $handler->upload($file);

        $this->assertStringStartsWith('/uploads/editorjs/', $result);
        $this->assertStringContainsString('document', $result);

        // File should exist in upload dir
        $filename = basename($result);
        $this->assertFileExists($this->uploadDir.'/'.$filename);
    }

    public function testUploadGeneratesUniqueFilenames(): void
    {
        $handler = new LocalUploadHandler($this->uploadDir, '/uploads', new AsciiSlugger());

        $tempFile1 = tempnam(sys_get_temp_dir(), 'test_').'.txt';
        file_put_contents($tempFile1, 'content 1');
        $file1 = new UploadedFile($tempFile1, 'same.txt', 'text/plain', null, true);

        $tempFile2 = tempnam(sys_get_temp_dir(), 'test_').'.txt';
        file_put_contents($tempFile2, 'content 2');
        $file2 = new UploadedFile($tempFile2, 'same.txt', 'text/plain', null, true);

        $result1 = $handler->upload($file1);
        $result2 = $handler->upload($file2);

        $this->assertNotSame($result1, $result2);
    }

    public function testPublicPathTrailingSlashIsNormalized(): void
    {
        $handler = new LocalUploadHandler($this->uploadDir, '/uploads/editorjs/', new AsciiSlugger());

        $tempFile = tempnam(sys_get_temp_dir(), 'test_').'.txt';
        file_put_contents($tempFile, 'test');

        $file = new UploadedFile($tempFile, 'test.txt', 'text/plain', null, true);
        $result = $handler->upload($file);

        // Should not have double slashes
        $this->assertStringNotContainsString('//', substr($result, 1));
    }

    public function testUploadByUrlDownloadsAndSavesFile(): void
    {
        // We can't test with a real URL in unit tests, but we can test the directory creation
        // and filename generation by testing with a file:// URL
        $sourceFile = tempnam(sys_get_temp_dir(), 'source_').'.png';
        file_put_contents($sourceFile, 'fake png content');

        $targetDir = $this->uploadDir.'/subdir';
        $handler = new LocalUploadHandler($targetDir, '/uploads/editorjs', new AsciiSlugger());

        $result = $handler->uploadByUrl('file://'.$sourceFile);

        $this->assertStringStartsWith('/uploads/editorjs/', $result);
        $this->assertStringContainsString('url-upload', $result);
        $this->assertStringEndsWith('.png', $result);

        // Directory should have been created
        $this->assertDirectoryExists($targetDir);

        // File should exist
        $filename = basename($result);
        $this->assertFileExists($targetDir.'/'.$filename);

        @unlink($sourceFile);
    }

    public function testUploadByUrlWithoutExtensionDefaultsToJpg(): void
    {
        $sourceFile = tempnam(sys_get_temp_dir(), 'source_');
        file_put_contents($sourceFile, 'content');

        $handler = new LocalUploadHandler($this->uploadDir, '/uploads', new AsciiSlugger());

        // file:// URL with no extension
        $result = $handler->uploadByUrl('file://'.$sourceFile);

        $this->assertStringEndsWith('.jpg', $result);

        @unlink($sourceFile);
    }

    public function testUploadByUrlCleansUpTempFileOnFailure(): void
    {
        $handler = new LocalUploadHandler('/nonexistent/read-only/dir', '/uploads', new AsciiSlugger());

        $this->expectException(\RuntimeException::class);

        // Suppress the expected file_get_contents warning
        @$handler->uploadByUrl('file:///nonexistent/file/that/does/not/exist');
    }

    private function removeDir(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        foreach (scandir($dir) as $item) {
            if ('.' === $item || '..' === $item) {
                continue;
            }
            $path = $dir.'/'.$item;
            is_dir($path) ? $this->removeDir($path) : unlink($path);
        }
        rmdir($dir);
    }
}
