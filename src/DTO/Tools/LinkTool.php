<?php

namespace Makraz\EditorjsBundle\DTO\Tools;

final class LinkTool extends AbstractTool
{
    public function __construct(
        private readonly ?string $fetchEndpoint = null,
    ) {
    }

    public function getName(): string
    {
        return 'linkTool';
    }

    public function getConfig(): array
    {
        $config = [];

        if (null !== $this->fetchEndpoint) {
            $config['endpoint'] = $this->fetchEndpoint;
        }

        return $config;
    }
}
