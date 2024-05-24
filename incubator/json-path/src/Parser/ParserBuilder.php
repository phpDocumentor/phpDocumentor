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

namespace phpDocumentor\JsonPath\Parser;

use Parsica\Parsica\Parser;
use phpDocumentor\JsonPath\AST\Comparison;
use phpDocumentor\JsonPath\AST\CurrentNode;
use phpDocumentor\JsonPath\AST\FieldAccess;
use phpDocumentor\JsonPath\AST\FieldName;
use phpDocumentor\JsonPath\AST\FilterNode;
use phpDocumentor\JsonPath\AST\FunctionCall;
use phpDocumentor\JsonPath\AST\Path;
use phpDocumentor\JsonPath\AST\RootNode;
use phpDocumentor\JsonPath\AST\Value;
use phpDocumentor\JsonPath\AST\Wildcard;

use function is_array;
use function Parsica\Parsica\alphaNumChar;
use function Parsica\Parsica\any;
use function Parsica\Parsica\atLeastOne;
use function Parsica\Parsica\between;
use function Parsica\Parsica\char;
use function Parsica\Parsica\choice;
use function Parsica\Parsica\collect;
use function Parsica\Parsica\keepSecond;
use function Parsica\Parsica\noneOfS;
use function Parsica\Parsica\optional;
use function Parsica\Parsica\recursive;
use function Parsica\Parsica\sepBy;
use function Parsica\Parsica\skipHSpace;
use function Parsica\Parsica\some;
use function Parsica\Parsica\string;
use function Parsica\Parsica\whitespace;

final class ParserBuilder
{
    /** @return Parser<RootNode> */
    private static function rootNode(): Parser
    {
        return char('$')->map(static fn () => new RootNode())->label('$');
    }

    /** @return Parser<CurrentNode> */
    private static function currentNode(): Parser
    {
        return char('@')->map(static fn () => new CurrentNode());
    }

    /** @return Parser<FieldAccess> */
    private static function fieldAccess(): Parser
    {
        $fieldName = self::fieldName();

        return choice(
            keepSecond(char('.'), any($fieldName, self::wildcard())),
            between(string("['"), string("']"), $fieldName),
        )->map(static fn ($args) => new FieldAccess($args));
    }

    /** @return Parser<Wildcard> */
    private static function wildcard(): Parser
    {
        return string('*')->label('Wildcard')->map(static fn () => new Wildcard());
    }

    /** @return Parser<FilterNode> */
    private static function filter(): Parser
    {
        return choice(
            between(
                string('['),
                string(']'),
                self::wildcard(),
            )->map(static fn ($wildcard) => new FilterNode($wildcard)),
            between(
                string('[?('),
                string(')]'),
                self::expression(),
            )->map((static fn ($expression) => new FilterNode($expression))),
        );
    }

    /** @return Parser<Comparison> */
    private static function expression(): Parser
    {
        $operator = choice(
            string('=='),
            string('!='),
            string('starts_with'),
            string('contains'),
        );

        $value = choice(
            between(char('"'), char('"'), atLeastOne(noneOfS('"')))
                ->map(static fn ($value) => new Value($value)),
            between(char("'"), char("'"), atLeastOne(noneOfS("'")))
                ->map(static fn ($value) => new Value($value)),
        )->label('VALUE');

        return collect(
            choice(
                self::currentNodeFollowUp(),
                self::functionCall(),
            ),
            optional(whitespace())->followedBy($operator),
            optional(whitespace())->followedBy($value),
        )->map(static fn ($args) => new Comparison($args[0], $args[1], $args[2]));
    }

    /** @return Parser<Path> */
    private static function currentNodeFollowUp(): Parser
    {
        $inner = choice(
            self::fieldAccess(),
        );

        return self::currentNode()->followedBy(
            some($inner)->map(static fn ($args) => is_array($args) ? $args : []),
        )->map(static fn ($args) => new Path([new CurrentNode(), ...$args]));
    }

    /** @return Parser<FunctionCall> */
    private static function functionCall(): Parser
    {
        return collect(
            atLeastOne(alphaNumChar()),
            skipHSpace()->followedBy(
                between(
                    char('('),
                    char(')'),
                    optional(self::arguments()),
                ),
            ),
        )->map(static fn ($a) => new FunctionCall($a[0], ...$a[1]));
    }

    /** @return Parser<list<mixed>> */
    private static function arguments(): Parser
    {
        return sepBy(
            char(','),
            choice(self::currentNodeFollowUp(), self::currentNode()),
        );
    }

    /** @return Parser<FieldName> */
    private static function fieldName(): Parser
    {
        return atLeastOne(
            alphaNumChar()->or(char('_')),
        )->label('NODE_NAME')->map(static fn ($name) => new FieldName($name));
    }

    /** @return Parser<Path> */
    private static function rootFollowUp(): Parser
    {
        $inner = choice(
            self::fieldAccess(),
            self::filter(),
        );

        $path = recursive();
        $path->recurse(collect($inner, $path));

        return collect(
            self::rootNode(),
            some($inner),
        )->map(
            static fn ($args) => new Path([$args[0], ...$args[1]]),
        );
    }

    /** @return Parser<Path> */
    public function build(): Parser
    {
        return choice(
            self::rootFollowUp(),
            self::currentNodeFollowUp(),
            self::rootNode(),
            self::currentNode(),
        )->thenEof()->label('End of Query');
    }
}
