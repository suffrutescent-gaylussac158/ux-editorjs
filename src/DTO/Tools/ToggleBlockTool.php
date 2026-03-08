<?php

namespace Makraz\EditorjsBundle\DTO\Tools;

final class ToggleBlockTool implements ToolInterface
{
    public function __construct(
        private readonly string $placeholder = 'Toggle title',
    ) {
    }

    public function getName(): string
    {
        return 'toggle';
    }

    public function getConfig(): array
    {
        return [
            'placeholder' => $this->placeholder,
        ];
    }

    public function getPackage(): string
    {
        return 'editorjs-toggle-block';
    }
}
