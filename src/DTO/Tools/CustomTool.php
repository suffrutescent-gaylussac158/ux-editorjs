<?php

namespace Makraz\EditorjsBundle\DTO\Tools;

final class CustomTool implements ToolInterface
{
    /**
     * @param array<string, mixed> $config
     */
    public function __construct(
        private readonly string $name,
        private readonly string $package,
        private readonly array $config = [],
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    public function getPackage(): ?string
    {
        return $this->package;
    }
}
