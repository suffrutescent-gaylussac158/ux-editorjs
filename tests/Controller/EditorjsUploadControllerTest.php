<?php

namespace Makraz\EditorjsBundle\Tests\Controller;

use Makraz\EditorjsBundle\Controller\EditorjsUploadController;
use Makraz\EditorjsBundle\Upload\UploadHandlerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class EditorjsUploadControllerTest extends TestCase
{
    public function testUploadByFileSuccess(): void
    {
        $handler = $this->createMock(UploadHandlerInterface::class);
        $handler->method('upload')->willReturn('/uploads/editorjs/test-abc123.jpg');

        $controller = new EditorjsUploadController($handler, 5 * 1024 * 1024, ['image/jpeg']);

        $tempFile = tempnam(sys_get_temp_dir(), 'test_').'.jpg';
        // Create a minimal valid JPEG (SOI + EOI markers)
        file_put_contents($tempFile, "\xFF\xD8\xFF\xE0\x00\x10JFIF\x00\x01\x01\x00\x00\x01\x00\x01\x00\x00\xFF\xD9");

        $file = new UploadedFile($tempFile, 'test.jpg', 'image/jpeg', null, true);

        $request = new Request(files: ['image' => $file]);
        $response = $controller->uploadByFile($request);

        $data = json_decode($response->getContent(), true);
        $this->assertSame(1, $data['success']);
        $this->assertSame('/uploads/editorjs/test-abc123.jpg', $data['file']['url']);

        @unlink($tempFile);
    }

    public function testUploadByFileNoFile(): void
    {
        $handler = $this->createMock(UploadHandlerInterface::class);
        $controller = new EditorjsUploadController($handler, 5 * 1024 * 1024, []);

        $request = new Request();
        $response = $controller->uploadByFile($request);

        $data = json_decode($response->getContent(), true);
        $this->assertSame(0, $data['success']);
        $this->assertSame('No file uploaded.', $data['message']);
        $this->assertSame(400, $response->getStatusCode());
    }

    public function testUploadByFileTooLarge(): void
    {
        $handler = $this->createMock(UploadHandlerInterface::class);
        $controller = new EditorjsUploadController($handler, 10, ['image/jpeg']);

        $tempFile = tempnam(sys_get_temp_dir(), 'test_');
        file_put_contents($tempFile, str_repeat('x', 100));

        $file = new UploadedFile($tempFile, 'large.jpg', 'image/jpeg', null, true);

        $request = new Request(files: ['image' => $file]);
        $response = $controller->uploadByFile($request);

        $data = json_decode($response->getContent(), true);
        $this->assertSame(0, $data['success']);
        $this->assertStringContainsString('File too large', $data['message']);

        @unlink($tempFile);
    }

    public function testUploadByFileDisallowedMimeType(): void
    {
        $handler = $this->createMock(UploadHandlerInterface::class);
        $controller = new EditorjsUploadController($handler, 5 * 1024 * 1024, ['image/jpeg']);

        $tempFile = tempnam(sys_get_temp_dir(), 'test_');
        file_put_contents($tempFile, 'not an image');

        $file = new UploadedFile($tempFile, 'test.txt', 'text/plain', null, true);

        $request = new Request(files: ['image' => $file]);
        $response = $controller->uploadByFile($request);

        $data = json_decode($response->getContent(), true);
        $this->assertSame(0, $data['success']);
        $this->assertStringContainsString('not allowed', $data['message']);

        @unlink($tempFile);
    }

    public function testUploadByUrlSuccess(): void
    {
        $handler = $this->createMock(UploadHandlerInterface::class);
        $handler->method('uploadByUrl')->willReturn('/uploads/editorjs/url-upload-abc123.jpg');

        $controller = new EditorjsUploadController($handler, 5 * 1024 * 1024, []);

        $request = new Request(content: json_encode(['url' => 'https://example.com/image.jpg']));
        $response = $controller->uploadByUrl($request);

        $data = json_decode($response->getContent(), true);
        $this->assertSame(1, $data['success']);
        $this->assertSame('/uploads/editorjs/url-upload-abc123.jpg', $data['file']['url']);
    }

    public function testUploadByUrlNoUrl(): void
    {
        $handler = $this->createMock(UploadHandlerInterface::class);
        $controller = new EditorjsUploadController($handler, 5 * 1024 * 1024, []);

        $request = new Request(content: json_encode([]));
        $response = $controller->uploadByUrl($request);

        $data = json_decode($response->getContent(), true);
        $this->assertSame(0, $data['success']);
        $this->assertSame('No URL provided.', $data['message']);
    }

    public function testUploadByUrlInvalidUrl(): void
    {
        $handler = $this->createMock(UploadHandlerInterface::class);
        $controller = new EditorjsUploadController($handler, 5 * 1024 * 1024, []);

        $request = new Request(content: json_encode(['url' => 'not-a-url']));
        $response = $controller->uploadByUrl($request);

        $data = json_decode($response->getContent(), true);
        $this->assertSame(0, $data['success']);
        $this->assertSame('Invalid URL.', $data['message']);
    }

    public function testUploadByFileHandlerExceptionReturnsError(): void
    {
        $handler = $this->createMock(UploadHandlerInterface::class);
        $handler->method('upload')->willThrowException(new \RuntimeException('Disk full'));

        $controller = new EditorjsUploadController($handler, 5 * 1024 * 1024, []);

        $tempFile = tempnam(sys_get_temp_dir(), 'test_').'.jpg';
        file_put_contents($tempFile, "\xFF\xD8\xFF\xE0\x00\x10JFIF\x00\x01\x01\x00\x00\x01\x00\x01\x00\x00\xFF\xD9");

        $file = new UploadedFile($tempFile, 'test.jpg', 'image/jpeg', null, true);

        $request = new Request(files: ['image' => $file]);
        $response = $controller->uploadByFile($request);

        $data = json_decode($response->getContent(), true);
        $this->assertSame(0, $data['success']);
        $this->assertSame('Disk full', $data['message']);
        $this->assertSame(400, $response->getStatusCode());

        @unlink($tempFile);
    }

    public function testUploadByUrlHandlerExceptionReturnsError(): void
    {
        $handler = $this->createMock(UploadHandlerInterface::class);
        $handler->method('uploadByUrl')->willThrowException(new \RuntimeException('Download failed'));

        $controller = new EditorjsUploadController($handler, 5 * 1024 * 1024, []);

        $request = new Request(content: json_encode(['url' => 'https://example.com/image.jpg']));
        $response = $controller->uploadByUrl($request);

        $data = json_decode($response->getContent(), true);
        $this->assertSame(0, $data['success']);
        $this->assertSame('Download failed', $data['message']);
        $this->assertSame(400, $response->getStatusCode());
    }

    public function testUploadByFileWithEmptyAllowedMimeTypesAcceptsAny(): void
    {
        $handler = $this->createMock(UploadHandlerInterface::class);
        $handler->method('upload')->willReturn('/uploads/file.txt');

        $controller = new EditorjsUploadController($handler, 5 * 1024 * 1024, []);

        $tempFile = tempnam(sys_get_temp_dir(), 'test_');
        file_put_contents($tempFile, 'plain text');

        $file = new UploadedFile($tempFile, 'test.txt', 'text/plain', null, true);

        $request = new Request(files: ['image' => $file]);
        $response = $controller->uploadByFile($request);

        $data = json_decode($response->getContent(), true);
        $this->assertSame(1, $data['success']);

        @unlink($tempFile);
    }

    public function testUploadByFileInvalidFile(): void
    {
        $handler = $this->createMock(UploadHandlerInterface::class);
        $controller = new EditorjsUploadController($handler, 5 * 1024 * 1024, []);

        $tempFile = tempnam(sys_get_temp_dir(), 'test_');
        file_put_contents($tempFile, 'data');

        // Create an UploadedFile with an error (UPLOAD_ERR_PARTIAL)
        $file = new UploadedFile($tempFile, 'test.txt', 'text/plain', \UPLOAD_ERR_PARTIAL);

        $request = new Request(files: ['image' => $file]);
        $response = $controller->uploadByFile($request);

        $data = json_decode($response->getContent(), true);
        $this->assertSame(0, $data['success']);
        $this->assertSame(400, $response->getStatusCode());

        @unlink($tempFile);
    }

    public function testUploadByUrlEmptyStringUrl(): void
    {
        $handler = $this->createMock(UploadHandlerInterface::class);
        $controller = new EditorjsUploadController($handler, 5 * 1024 * 1024, []);

        $request = new Request(content: json_encode(['url' => '']));
        $response = $controller->uploadByUrl($request);

        $data = json_decode($response->getContent(), true);
        $this->assertSame(0, $data['success']);
        $this->assertSame('No URL provided.', $data['message']);
    }
}
