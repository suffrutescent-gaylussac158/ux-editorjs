<?php

namespace Makraz\EditorjsBundle\DTO\Tools;

final class HeaderTool extends AbstractTool
{
    /**
     * @param list<int> $levels
     */
    public function __construct(
        private readonly string $placeholder = 'Enter a header',
        private readonly array $levels = [1, 2, 3, 4, 5, 6],
        private readonly int $defaultLevel = 2,
    ) {
    }

    public function getName(): string
    {
        return 'header';
    }

    public function getConfig(): array
    {
        return [
            'placeholder' => $this->placeholder,
            'levels' => $this->levels,
            'defaultLevel' => $this->defaultLevel,
        ];
    }
}
