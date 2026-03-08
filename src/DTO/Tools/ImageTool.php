<?php

namespace Makraz\EditorjsBundle\DTO\Tools;

final class ImageTool extends AbstractTool
{
    public function __construct(
        private readonly ?string $uploadEndpoint = null,
        private readonly ?string $uploadByUrlEndpoint = null,
        private readonly bool $captionPlaceholder = true,
        private readonly bool $withBorder = false,
        private readonly bool $stretched = false,
        private readonly bool $withBackground = false,
    ) {
    }

    public function getName(): string
    {
        return 'image';
    }

    public function getConfig(): array
    {
        $config = [
            'actions' => [
                'withBorder' => $this->withBorder,
                'stretched' => $this->stretched,
                'withBackground' => $this->withBackground,
            ],
        ];

        if (null !== $this->uploadEndpoint) {
            $config['endpoints'] = [
                'byFile' => $this->uploadEndpoint,
            ];

            if (null !== $this->uploadByUrlEndpoint) {
                $config['endpoints']['byUrl'] = $this->uploadByUrlEndpoint;
            }
        }

        return $config;
    }
}
