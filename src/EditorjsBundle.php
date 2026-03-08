<?php

namespace Makraz\EditorjsBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class EditorjsBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
