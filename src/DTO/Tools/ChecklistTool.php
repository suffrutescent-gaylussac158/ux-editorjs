<?php

namespace Makraz\EditorjsBundle\DTO\Tools;

final class ChecklistTool extends AbstractTool
{
    public function getName(): string
    {
        return 'checklist';
    }

    public function getConfig(): array
    {
        return [];
    }
}
