<?php

namespace Makraz\EditorjsBundle\DTO\Tools;

final class TextVariantTune implements TuneInterface
{
    public function getName(): string
    {
        return 'textVariant';
    }

    public function getConfig(): array
    {
        return [];
    }

    public function getPackage(): string
    {
        return '@editorjs/text-variant-tune';
    }
}
