<?php

namespace Makraz\EditorjsBundle\Controller;

use Makraz\EditorjsBundle\Upload\UploadHandlerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class EditorjsUploadController
{
    /**
     * @param list<string> $allowedMimeTypes
     */
    public function __construct(
        private readonly UploadHandlerInterface $uploadHandler,
        private readonly int $maxFileSize,
        private readonly array $allowedMimeTypes,
    ) {
    }

    public function uploadByFile(Request $request): JsonResponse
    {
        $file = $request->files->get('image');

        if (null === $file) {
            return $this->errorResponse('No file uploaded.');
        }

        if (!$file->isValid()) {
            return $this->errorResponse($file->getErrorMessage());
        }

        if ($file->getSize() > $this->maxFileSize) {
            return $this->errorResponse(\sprintf('File too large. Maximum size: %d MB.', $this->maxFileSize / 1024 / 1024));
        }

        $mimeType = $file->getMimeType();
        if ([] !== $this->allowedMimeTypes && !\in_array($mimeType, $this->allowedMimeTypes, true)) {
            return $this->errorResponse(\sprintf('File type "%s" is not allowed.', $mimeType));
        }

        try {
            $url = $this->uploadHandler->upload($file);

            return new JsonResponse([
                'success' => 1,
                'file' => ['url' => $url],
            ]);
        } catch (\Throwable $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function uploadByUrl(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $url = $data['url'] ?? null;

        if (null === $url || '' === $url) {
            return $this->errorResponse('No URL provided.');
        }

        if (!filter_var($url, \FILTER_VALIDATE_URL)) {
            return $this->errorResponse('Invalid URL.');
        }

        try {
            $fileUrl = $this->uploadHandler->uploadByUrl($url);

            return new JsonResponse([
                'success' => 1,
                'file' => ['url' => $fileUrl],
            ]);
        } catch (\Throwable $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    private function errorResponse(string $message): JsonResponse
    {
        return new JsonResponse([
            'success' => 0,
            'message' => $message,
        ], Response::HTTP_BAD_REQUEST);
    }
}
