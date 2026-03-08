<?php

namespace Makraz\EditorjsBundle\DTO\Tools;

final class AlignmentBlockTune implements TuneInterface
{
    public function __construct(
        private readonly string $default = 'left',
    ) {
    }

    public function getName(): string
    {
        return 'textAlignment';
    }

    public function getConfig(): array
    {
        return [
            'default' => $this->default,
        ];
    }

    public function getPackage(): string
    {
        return 'editorjs-alignment-blocktune';
    }
}
