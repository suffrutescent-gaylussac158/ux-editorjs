<?php

namespace Makraz\EditorjsBundle\DTO\Tools;

final class IndentTune implements TuneInterface
{
    public function __construct(
        private readonly int $maxIndent = 5,
        private readonly int $indentSize = 24,
        private readonly string $direction = 'ltr',
    ) {
    }

    public function getName(): string
    {
        return 'indentTune';
    }

    public function getConfig(): array
    {
        return [
            'maxIndent' => $this->maxIndent,
            'indentSize' => $this->indentSize,
            'direction' => $this->direction,
        ];
    }

    public function getPackage(): string
    {
        return 'editorjs-indent-tune';
    }
}
