<?php

namespace Makraz\EditorjsBundle\DTO\Tools;

final class UnderlineTool extends AbstractTool
{
    public function getName(): string
    {
        return 'underline';
    }

    public function getConfig(): array
    {
        return [];
    }
}
