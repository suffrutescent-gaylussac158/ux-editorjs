<?php

namespace Makraz\EditorjsBundle\DTO\Tools;

interface ToolInterface
{
    public function getName(): string;

    /**
     * @return array<string, mixed>
     */
    public function getConfig(): array;

    /**
     * Returns the npm package name for this tool.
     * Return null to use the built-in tool mapping.
     */
    public function getPackage(): ?string;
}
