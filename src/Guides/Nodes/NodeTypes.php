<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor\Guides\Nodes;

final class NodeTypes
{
    public const DOCUMENT = 'document';
    public const DOCUMENT_NODE = 'document_node';
    public const TOC = 'toc';
    public const TITLE = 'title';
    public const SEPARATOR = 'separator';
    public const CODE = 'code';
    public const QUOTE = 'quote';
    public const PARAGRAPH = 'paragraph';
    public const ANCHOR = 'anchor';
    public const LIST = 'list';
    public const TABLE = 'table';
    public const SPAN = 'span';
    public const DEFINITION_LIST = 'definition_list';
    public const WRAPPER = 'wrapper';
    public const FIGURE = 'figure';
    public const IMAGE = 'image';
    public const META = 'meta';
    public const RAW = 'raw';
    public const DUMMY = 'dummy';
    public const MAIN = 'main';
    public const BLOCK = 'block';
    public const CALLABLE = 'callable';
    public const SECTION_BEGIN = 'section_begin';
    public const SECTION_END = 'section_end';

    public const NODES = [
        self::DOCUMENT,
        self::DOCUMENT_NODE,
        self::TOC,
        self::TITLE,
        self::SEPARATOR,
        self::CODE,
        self::QUOTE,
        self::PARAGRAPH,
        self::ANCHOR,
        self::LIST,
        self::TABLE,
        self::SPAN,
        self::DEFINITION_LIST,
        self::WRAPPER,
        self::FIGURE,
        self::IMAGE,
        self::META,
        self::RAW,
        self::DUMMY,
        self::MAIN,
        self::BLOCK,
        self::CALLABLE,
        self::SECTION_BEGIN,
        self::SECTION_END,
    ];

    private function __construct()
    {
    }
}
