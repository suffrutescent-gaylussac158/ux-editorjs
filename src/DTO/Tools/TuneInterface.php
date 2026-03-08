<?php

namespace Makraz\EditorjsBundle\DTO\Tools;

/**
 * Marker interface for Editor.js Block Tunes.
 *
 * Tunes are registered in the tools config like regular tools,
 * but are additionally listed in the top-level "tunes" array
 * of the EditorJS config for global application to all blocks.
 */
interface TuneInterface extends ToolInterface
{
}
