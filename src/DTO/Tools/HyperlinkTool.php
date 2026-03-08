<?php

namespace Makraz\EditorjsBundle\DTO\Tools;

/**
 * Inline link tool with target and rel attribute support.
 *
 * An enhanced alternative to the built-in link tool,
 * allowing users to set target (_blank, _self) and rel (noopener, noreferrer).
 */
final class HyperlinkTool implements ToolInterface
{
    /**
     * @param list<string>|null $availableTargets
     * @param list<string>|null $availableRels
     */
    public function __construct(
        private readonly string $shortcut = 'CMD+K',
        private readonly ?string $target = null,
        private readonly ?string $rel = null,
        private readonly ?array $availableTargets = null,
        private readonly ?array $availableRels = null,
    ) {
    }

    public function getName(): string
    {
        return 'hyperlink';
    }

    public function getConfig(): array
    {
        $config = [
            'shortcut' => $this->shortcut,
        ];

        if (null !== $this->target) {
            $config['target'] = $this->target;
        }

        if (null !== $this->rel) {
            $config['rel'] = $this->rel;
        }

        if (null !== $this->availableTargets) {
            $config['availableTargets'] = $this->availableTargets;
        }

        if (null !== $this->availableRels) {
            $config['availableRels'] = $this->availableRels;
        }

        return $config;
    }

    public function getPackage(): string
    {
        return 'editorjs-hyperlink';
    }
}
