<?php

namespace Makraz\EditorjsBundle\DTO\Tools;

final class CodeTool extends AbstractTool
{
    public function __construct(
        private readonly string $placeholder = 'Enter code',
    ) {
    }

    public function getName(): string
    {
        return 'code';
    }

    public function getConfig(): array
    {
        return [
            'placeholder' => $this->placeholder,
        ];
    }
}
