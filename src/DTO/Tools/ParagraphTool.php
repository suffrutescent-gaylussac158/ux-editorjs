<?php

namespace Makraz\EditorjsBundle\DTO\Tools;

final class ParagraphTool extends AbstractTool
{
    public function __construct(
        private readonly string $placeholder = '',
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
            'preserveBlank' => $this->preserveBlank,
        ];
    }
}
