<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Parser;

/**
 * "States" for DocumentParser as it parses line-by-line.
 */
class State
{
    /**
     * There is currently no state: the next line will begin a new state
     */
    public const BEGIN = 'begin';
    /**
     * Normal, non-indented, non-table lines
     */
    public const NORMAL    = 'normal';
    public const DIRECTIVE = 'directive';
    /**
     * Indented lines
     */
    public const BLOCK           = 'block';
    public const TITLE           = 'title';
    public const LIST            = 'list';
    public const SEPARATOR       = 'separator';
    public const CODE            = 'code';
    public const TABLE           = 'table';
    public const COMMENT         = 'comment';
    public const DEFINITION_LIST = 'definition_list';
}
