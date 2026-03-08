<?php

namespace Makraz\EditorjsBundle\DTO\Tools;

final class LinkAutocompleteTool implements ToolInterface
{
    public function __construct(
        private readonly ?string $endpoint = null,
        private readonly string $queryParam = 'search',
    ) {
    }

    public function getName(): string
    {
        return 'linkAutocomplete';
    }

    public function getConfig(): array
    {
        $config = [];

        if (null !== $this->endpoint) {
            $config['endpoint'] = $this->endpoint;
            $config['queryParam'] = $this->queryParam;
        }

        return $config;
    }

    public function getPackage(): string
    {
        return '@editorjs/link-autocomplete';
    }
}
