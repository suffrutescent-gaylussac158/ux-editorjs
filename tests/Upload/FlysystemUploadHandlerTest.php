<?php

namespace Makraz\EditorjsBundle\Tests\Upload;

use League\Flysystem\FilesystemOperator;
use Makraz\EditorjsBundle\Upload\FlysystemUploadHandler;
use Makraz\EditorjsBundle\Upload\UploadHandlerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\AsciiSlugger;

class FlysystemUploadHandlerTest extends TestCase
{
    public function testImplementsInterface(): void
    {
        $filesystem = $this->createMock(FilesystemOperator::class);
        $handler = new FlysystemUploadHandler($filesystem, 'uploads/editorjs', 'https://cdn.example.com', new AsciiSlugger());

        $this->assertInstanceOf(UploadHandlerInterface::class, $handler);
    }

    public function testUploadWritesStreamToFilesystem(): void
    {
        $filesystem = $this->createMock(FilesystemOperator::class);
        $filesystem->expects($this->once())
            ->method('writeStream')
            ->with(
                $this->stringStartsWith('uploads/editorjs/'),
                $this->isType('resource'),
            );

        $handler = new FlysystemUploadHandler($filesystem, 'uploads/editorjs', 'https://cdn.example.com', new AsciiSlugger());

        $tempFile = tempnam(sys_get_temp_dir(), 'test_').'.jpg';
        file_put_contents($tempFile, 'fake image data');
        $file = new UploadedFile($tempFile, 'photo.jpg', 'image/jpeg', null, true);

        $result = $handler->upload($file);

        $this->assertStringStartsWith('https://cdn.example.com/', $result);
        $this->assertStringContainsString('photo', $result);

        @unlink($tempFile);
    }

    public function testUploadPublicUrlTrailingSlashNormalized(): void
    {
        $filesystem = $this->createMock(FilesystemOperator::class);
        $filesystem->method('writeStream');

        $handler = new FlysystemUploadHandler($filesystem, 'uploads/editorjs', 'https://cdn.example.com/', new AsciiSlugger());

        $tempFile = tempnam(sys_get_temp_dir(), 'test_').'.txt';
        file_put_contents($tempFile, 'content');
        $file = new UploadedFile($tempFile, 'file.txt', 'text/plain', null, true);

        $result = $handler->upload($file);

        // No double slashes after protocol
        $afterProtocol = substr($result, \strlen('https://'));
        $this->assertStringNotContainsString('//', $afterProtocol);

        @unlink($tempFile);
    }

    public function testUploadGeneratesUniqueFilenames(): void
    {
        $paths = [];
        $filesystem = $this->createMock(FilesystemOperator::class);
        $filesystem->method('writeStream')->willReturnCallback(static function (string $path) use (&$paths) {
            $paths[] = $path;
        });

        $handler = new FlysystemUploadHandler($filesystem, 'uploads', 'https://cdn.example.com', new AsciiSlugger());

        for ($i = 0; $i < 2; ++$i) {
            $tempFile = tempnam(sys_get_temp_dir(), 'test_').'.txt';
            file_put_contents($tempFile, "content $i");
            $file = new UploadedFile($tempFile, 'same.txt', 'text/plain', null, true);
            $handler->upload($file);
            @unlink($tempFile);
        }

        $this->assertCount(2, $paths);
        $this->assertNotSame($paths[0], $paths[1]);
    }

    public function testUploadByUrlWritesToFilesystem(): void
    {
        $sourceFile = tempnam(sys_get_temp_dir(), 'source_').'.png';
        file_put_contents($sourceFile, 'fake png content');

        $filesystem = $this->createMock(FilesystemOperator::class);
        $filesystem->expects($this->once())
            ->method('write')
            ->with(
                $this->stringStartsWith('uploads/editorjs/'),
                'fake png content',
            );

        $handler = new FlysystemUploadHandler($filesystem, 'uploads/editorjs', 'https://cdn.example.com', new AsciiSlugger());

        $result = $handler->uploadByUrl('file://'.$sourceFile);

        $this->assertStringStartsWith('https://cdn.example.com/', $result);
        $this->assertStringContainsString('url-upload', $result);
        $this->assertStringEndsWith('.png', $result);

        @unlink($sourceFile);
    }

    public function testUploadByUrlDefaultsToJpgExtension(): void
    {
        $sourceFile = tempnam(sys_get_temp_dir(), 'source_');
        file_put_contents($sourceFile, 'content');

        $filesystem = $this->createMock(FilesystemOperator::class);
        $filesystem->method('write');

        $handler = new FlysystemUploadHandler($filesystem, 'uploads', 'https://cdn.example.com', new AsciiSlugger());

        $result = $handler->uploadByUrl('file://'.$sourceFile);

        $this->assertStringEndsWith('.jpg', $result);

        @unlink($sourceFile);
    }

    public function testUploadByUrlThrowsOnFailedDownload(): void
    {
        $filesystem = $this->createMock(FilesystemOperator::class);
        $handler = new FlysystemUploadHandler($filesystem, 'uploads', 'https://cdn.example.com', new AsciiSlugger());

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Could not download file from URL');

        @$handler->uploadByUrl('file:///nonexistent/file');
    }

    public function testUploadPathTrailingSlashNormalized(): void
    {
        $filesystem = $this->createMock(FilesystemOperator::class);
        $filesystem->method('writeStream');

        $handler = new FlysystemUploadHandler($filesystem, 'uploads/editorjs/', 'https://cdn.example.com', new AsciiSlugger());

        $tempFile = tempnam(sys_get_temp_dir(), 'test_').'.txt';
        file_put_contents($tempFile, 'content');
        $file = new UploadedFile($tempFile, 'test.txt', 'text/plain', null, true);

        $result = $handler->upload($file);

        // The upload path in the filesystem should not have double slashes
        $this->assertStringNotContainsString('//', substr($result, \strlen('https://')));

        @unlink($tempFile);
    }
}
