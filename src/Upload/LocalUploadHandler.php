<?php

namespace Makraz\EditorjsBundle\Upload;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

final class LocalUploadHandler implements UploadHandlerInterface
{
    public function __construct(
        private readonly string $uploadDir,
        private readonly string $publicPath,
        private readonly SluggerInterface $slugger,
    ) {
    }

    public function upload(UploadedFile $file): string
    {
        $filename = $this->generateFilename($file->getClientOriginalName(), $file->guessExtension() ?? 'bin');

        $file->move($this->uploadDir, $filename);

        return rtrim($this->publicPath, '/').'/'.$filename;
    }

    public function uploadByUrl(string $url): string
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'editorjs_');

        try {
            $content = file_get_contents($url);
            if (false === $content) {
                throw new \RuntimeException(\sprintf('Could not download file from URL: %s', $url));
            }

            file_put_contents($tempFile, $content);

            $extension = pathinfo(parse_url($url, \PHP_URL_PATH) ?? '', \PATHINFO_EXTENSION) ?: 'jpg';
            $filename = $this->generateFilename('url-upload', $extension);

            if (!is_dir($this->uploadDir)) {
                mkdir($this->uploadDir, 0o777, true);
            }

            rename($tempFile, $this->uploadDir.'/'.$filename);

            return rtrim($this->publicPath, '/').'/'.$filename;
        } catch (\Throwable $e) {
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }

            throw $e;
        }
    }

    private function generateFilename(string $originalName, string $extension): string
    {
        $safeName = $this->slugger->slug(pathinfo($originalName, \PATHINFO_FILENAME));

        return \sprintf('%s-%s.%s', $safeName, bin2hex(random_bytes(8)), $extension);
    }
}
