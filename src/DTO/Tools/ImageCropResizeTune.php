<?php

namespace Makraz\EditorjsBundle\DTO\Tools;

/**
 * Image crop, resize, rotate and flip tune.
 *
 * Non-destructive editing: the original image is preserved until changes are applied.
 * Cropped images can be stored as base64 directly in Editor.js output data.
 *
 * @see https://github.com/mohdaffann/editorjs-image-crop-resize
 */
final class ImageCropResizeTune implements PerToolTuneInterface
{
    public function getName(): string
    {
        return 'CropperTune';
    }

    public function getConfig(): array
    {
        return [];
    }

    public function getPackage(): string
    {
        return 'editorjs-image-crop-resize';
    }

    public function getApplicableTools(): array
    {
        return ['image'];
    }
}
