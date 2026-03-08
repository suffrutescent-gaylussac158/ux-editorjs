<?php

namespace Makraz\EditorjsBundle\DTO\Tools;

final class WarningTool extends AbstractTool
{
    public function __construct(
        private readonly string $titlePlaceholder = 'Title',
        private readonly string $messagePlaceholder = 'Message',
    ) {
    }

    public function getName(): string
    {
        return 'warning';
    }

    public function getConfig(): array
    {
        return [
            'titlePlaceholder' => $this->titlePlaceholder,
            'messagePlaceholder' => $this->messagePlaceholder,
        ];
    }
}
