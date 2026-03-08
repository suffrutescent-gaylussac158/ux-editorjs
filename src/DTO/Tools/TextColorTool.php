<?php

namespace Makraz\EditorjsBundle\DTO\Tools;

/**
 * Inline tool for coloring or highlighting text.
 *
 * The editorjs-text-color-plugin supports two modes:
 * - type "text": changes the text color (registers as tool name "textColor")
 * - type "marker": highlights text background (registers as tool name "colorMarker")
 *
 * You can add both by creating two instances with different types.
 */
final class TextColorTool implements ToolInterface
{
    public function __construct(
        private readonly string $defaultColor = '#FF1300',
        private readonly string $type = 'text',
    ) {
    }

    public function getName(): string
    {
        return 'text' === $this->type ? 'textColor' : 'colorMarker';
    }

    public function getConfig(): array
    {
        return [
            'defaultColor' => $this->defaultColor,
            'type' => $this->type,
        ];
    }

    public function getPackage(): string
    {
        return 'editorjs-text-color-plugin';
    }
}
