<?php

namespace Makraz\EditorjsBundle\DTO\Tools;

final class StrikethroughTool implements ToolInterface
{
    public function getName(): string
    {
        return 'strikethrough';
    }

    public function getConfig(): array
    {
        return [];
    }

    public function getPackage(): string
    {
        return '@sotaproject/strikethrough';
    }
}
