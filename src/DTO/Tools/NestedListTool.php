<?php

namespace Makraz\EditorjsBundle\DTO\Tools;

final class NestedListTool implements ToolInterface
{
    public function __construct(
        private readonly string $defaultStyle = 'unordered',
    ) {
    }

    public function getName(): string
    {
        return 'list';
    }

    public function getConfig(): array
    {
        return [
            'defaultStyle' => $this->defaultStyle,
        ];
    }

    public function getPackage(): string
    {
        return '@editorjs/nested-list';
    }
}
