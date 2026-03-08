<?php

namespace Makraz\EditorjsBundle\DTO\Tools;

final class MarkerTool extends AbstractTool
{
    public function getName(): string
    {
        return 'marker';
    }

    public function getConfig(): array
    {
        return [];
    }
}
