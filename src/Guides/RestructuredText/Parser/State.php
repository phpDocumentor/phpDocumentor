<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Parser;

class State
{
    public const BEGIN = 'begin';
    public const NORMAL = 'normal';
    public const DIRECTIVE = 'directive';
    public const BLOCK = 'block';
    public const TITLE = 'title';
    public const LIST = 'list';
    public const SEPARATOR = 'separator';
    public const CODE = 'code';
    public const TABLE = 'table';
    public const COMMENT = 'comment';
    public const DEFINITION_LIST = 'definition_list';
}
