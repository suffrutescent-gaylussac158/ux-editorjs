<?php

namespace Makraz\EditorjsBundle\DTO\Tools;

final class AlignmentParagraphTool implements ToolInterface
{
    public function __construct(
        private readonly string $placeholder = '',
        private readonly string $defaultAlignment = 'left',
        private readonly bool $preserveBlank = false,
    ) {
    }

    public function getName(): string
    {
        return 'paragraph';
    }

    public function getConfig(): array
    {
        return [
            'placeholder' => $this->placeholder,
            'defaultAlignment' => $this->defaultAlignment,
            'preserveBlank' => $this->preserveBlank,
        ];
    }

    public function getPackage(): string
    {
        return 'editorjs-paragraph-with-alignment';
    }
}
