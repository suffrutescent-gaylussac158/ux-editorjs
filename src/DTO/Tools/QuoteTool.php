<?php

namespace Makraz\EditorjsBundle\DTO\Tools;

final class QuoteTool extends AbstractTool
{
    public function __construct(
        private readonly string $quotePlaceholder = 'Enter a quote',
        private readonly string $captionPlaceholder = 'Quote\'s author',
    ) {
    }

    public function getName(): string
    {
        return 'quote';
    }

    public function getConfig(): array
    {
        return [
            'quotePlaceholder' => $this->quotePlaceholder,
            'captionPlaceholder' => $this->captionPlaceholder,
        ];
    }
}
