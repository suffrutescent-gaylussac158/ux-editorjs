<?php

namespace Makraz\EditorjsBundle\DTO\Tools;

final class RawTool extends AbstractTool
{
    public function __construct(
        private readonly string $placeholder = 'Enter raw HTML',
    ) {
    }

    public function getName(): string
    {
        return 'raw';
    }

    public function getConfig(): array
    {
        return [
            'placeholder' => $this->placeholder,
        ];
    }
}
