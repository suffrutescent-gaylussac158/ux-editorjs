<?php

namespace Makraz\EditorjsBundle\DTO\Tools;

final class EmbedTool extends AbstractTool
{
    /**
     * @param list<string> $services
     */
    public function __construct(
        private readonly array $services = ['youtube', 'vimeo', 'codepen', 'github'],
    ) {
    }

    public function getName(): string
    {
        return 'embed';
    }

    public function getConfig(): array
    {
        return [
            'services' => array_fill_keys($this->services, true),
        ];
    }
}
