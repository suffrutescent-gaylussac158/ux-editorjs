<?php

namespace Makraz\EditorjsBundle\DTO\Enums;

enum EditorjsTool: string
{
    case HEADER = 'header';
    case LIST = 'list';
    case PARAGRAPH = 'paragraph';
    case IMAGE = 'image';
    case CODE = 'code';
    case DELIMITER = 'delimiter';
    case QUOTE = 'quote';
    case WARNING = 'warning';
    case TABLE = 'table';
    case EMBED = 'embed';
    case MARKER = 'marker';
    case INLINE_CODE = 'inlineCode';
    case CHECKLIST = 'checklist';
    case LINK = 'linkTool';
    case RAW = 'raw';
    case UNDERLINE = 'underline';
}
