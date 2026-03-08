<?php

namespace Makraz\EditorjsBundle\DTO\Tools;

final class TableTool extends AbstractTool
{
    public function __construct(
        private readonly int $rows = 2,
        private readonly int $cols = 3,
        private readonly bool $withHeadings = true,
    ) {
    }

    public function getName(): string
    {
        return 'table';
    }

    public function getConfig(): array
    {
        return [
            'rows' => $this->rows,
            'cols' => $this->cols,
            'withHeadings' => $this->withHeadings,
        ];
    }
}
