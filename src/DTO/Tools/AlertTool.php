<?php

namespace Makraz\EditorjsBundle\DTO\Tools;

final class AlertTool implements ToolInterface
{
    public function __construct(
        private readonly string $defaultType = 'info',
        private readonly string $defaultAlign = 'left',
        private readonly string $messagePlaceholder = 'Enter alert message',
    ) {
    }

    public function getName(): string
    {
        return 'alert';
    }

    public function getConfig(): array
    {
        return [
            'defaultType' => $this->defaultType,
            'defaultAlign' => $this->defaultAlign,
            'messagePlaceholder' => $this->messagePlaceholder,
        ];
    }

    public function getPackage(): string
    {
        return 'editorjs-alert';
    }
}
