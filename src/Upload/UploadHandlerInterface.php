<?php

namespace Makraz\EditorjsBundle\Upload;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface UploadHandlerInterface
{
    /**
     * Handles an uploaded file and returns the public URL.
     */
    public function upload(UploadedFile $file): string;

    /**
     * Downloads an image from a URL and returns the public URL.
     */
    public function uploadByUrl(string $url): string;
}
