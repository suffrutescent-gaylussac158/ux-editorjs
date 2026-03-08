<?php

namespace Makraz\EditorjsBundle\DTO\Tools;

final class AttachesTool implements ToolInterface
{
    public function __construct(
        private readonly ?string $endpoint = null,
        private readonly string $field = 'file',
        private readonly string $buttonText = 'Select file',
        private readonly ?string $types = null,
        private readonly ?string $errorMessage = null,
    ) {
    }

    public function getName(): string
    {
        return 'attaches';
    }

    public function getConfig(): array
    {
        $config = [
            'field' => $this->field,
            'buttonText' => $this->buttonText,
        ];

        if (null !== $this->endpoint) {
            $config['endpoint'] = $this->endpoint;
        }

        if (null !== $this->types) {
            $config['types'] = $this->types;
        }

        if (null !== $this->errorMessage) {
            $config['errorMessage'] = $this->errorMessage;
        }

        return $config;
    }

    public function getPackage(): string
    {
        return '@editorjs/attaches';
    }
}
