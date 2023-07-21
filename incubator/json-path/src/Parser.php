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

namespace phpDocumentor\JsonPath;

use Parsica\Parsica\Parser as InnerParser;
use phpDocumentor\JsonPath\AST\Path;
use phpDocumentor\JsonPath\Parser\ParserBuilder;

final class Parser
{
    /** @param InnerParser<Path> $innerParser*/
    private function __construct(private readonly InnerParser $innerParser)
    {
    }

    public static function createInstance(): self
    {
        return new self(
            (new ParserBuilder())->build(),
        );
    }

    public function parse(string $query): Query
    {
        return new Query(
            $this->innerParser->tryString($query)->output(),
        );
    }
}
