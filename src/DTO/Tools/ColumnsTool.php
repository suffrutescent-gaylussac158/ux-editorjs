<?php

namespace Makraz\EditorjsBundle\DTO\Tools;

final class ColumnsTool implements ToolInterface
{
    public function __construct(
        private readonly ?array $tools = null,
    ) {
    }

    public function getName(): string
    {
        return 'columns';
    }

    public function getConfig(): array
    {
        $config = [
            'requireEditorJS' => true,
        ];

        if (null !== $this->tools) {
            $config['tools'] = $this->tools;
        }

        return $config;
    }

    public function getPackage(): string
    {
        return '@calumk/editorjs-columns';
    }
}
