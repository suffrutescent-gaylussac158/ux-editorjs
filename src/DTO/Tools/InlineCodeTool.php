<?php

namespace Makraz\EditorjsBundle\DTO\Tools;

final class InlineCodeTool extends AbstractTool
{
    public function getName(): string
    {
        return 'inlineCode';
    }

    public function getConfig(): array
    {
        return [];
    }
}
