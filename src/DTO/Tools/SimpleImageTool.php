<?php

namespace Makraz\EditorjsBundle\DTO\Tools;

final class SimpleImageTool implements ToolInterface
{
    public function getName(): string
    {
        return 'simpleImage';
    }

    public function getConfig(): array
    {
        return [];
    }

    public function getPackage(): string
    {
        return '@editorjs/simple-image';
    }
}
