<?php

namespace Makraz\EditorjsBundle\DTO\Tools;

/**
 * Interface for Block Tunes that apply to specific tools only.
 *
 * Unlike global TuneInterface tunes (applied to all blocks via EditorConfig.tunes),
 * per-tool tunes are added to specific tools' "tunes" array. For example,
 * an image crop tune only applies to the "image" tool.
 */
interface PerToolTuneInterface extends TuneInterface
{
    /**
     * Returns the tool names this tune should be applied to.
     *
     * @return list<string> e.g., ['image']
     */
    public function getApplicableTools(): array;
}
