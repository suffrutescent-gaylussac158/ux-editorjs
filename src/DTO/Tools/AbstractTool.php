<?php

namespace Makraz\EditorjsBundle\DTO\Tools;

abstract class AbstractTool implements ToolInterface
{
    public function getPackage(): ?string
    {
        return null;
    }
}
