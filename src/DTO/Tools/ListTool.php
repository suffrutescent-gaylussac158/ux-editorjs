<?php

namespace Makraz\EditorjsBundle\DTO\Tools;

final class ListTool extends AbstractTool
{
    public function __construct(
        private readonly string $defaultStyle = 'unordered',
        private readonly int $maxLevel = 3,
    ) {
    }

    public function getName(): string
    {
        return 'list';
    }

    public function getConfig(): array
    {
        return [
            'defaultStyle' => $this->defaultStyle,
            'maxLevel' => $this->maxLevel,
        ];
    }
}
