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
use phpDocumentor\JsonPath\AST\ExistsExpression;
use phpDocumentor\JsonPath\AST\FieldAccess;
use phpDocumentor\JsonPath\AST\FieldName;
use phpDocumentor\JsonPath\AST\FilterNode;
use phpDocumentor\JsonPath\AST\FunctionCall;
use phpDocumentor\JsonPath\AST\LogicalAnd;
use phpDocumentor\JsonPath\AST\LogicalOr;
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
use function Parsica\Parsica\Expression\binaryOperator;
use function Parsica\Parsica\Expression\expression;
use function Parsica\Parsica\Expression\leftAssoc;
use function Parsica\Parsica\keepFirst;
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
        static $parser = null;
        if ($parser !== null) {
            return $parser;
        }

        // Assign the recursive placeholder before building the body: filter() and currentNodeFollowUp()
        // reference each other (nested filters like `@.chapters[?(...)]`), so building the body eagerly
        // recurses back into filter() before the previous call returns. Returning the cached placeholder
        // breaks that cycle; ->recurse() below ties it to real behaviour once the body is ready.
        $parser = recursive();

        $token = static fn (Parser $parser): Parser => keepFirst($parser, skipHSpace());
        $parens = static fn (Parser $parser): Parser => $token(between($token(char('(')), $token(char(')')), $parser));

        $expr = recursive();
        $expr->recurse(expression(
            $parens($token($expr))
                ->or($token(self::expression()))
                ->or($token(self::existsExpression())),
            [
                leftAssoc(
                    binaryOperator(
                        $token(string('&&')),
                        static fn ($left, $right) => new LogicalAnd($left, $right),
                    ),
                ),
                leftAssoc(
                    binaryOperator(
                        $token(string('||')),
                        static fn ($left, $right) => new LogicalOr($left, $right),
                    ),
                ),
            ],
        ));

        $parser->recurse(choice(
            between(
                string('['),
                string(']'),
                self::wildcard(),
            )->map(static fn ($wildcard) => new FilterNode($wildcard)),
            between(
                string('[?('),
                string(')]'),
                $expr,
            )->map(static fn ($expression) => new FilterNode($expression)),
        ));

        return $parser;
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
            self::filter(),
        );

        return self::currentNode()->followedBy(
            some($inner)->map(static fn ($args) => is_array($args) ? $args : []),
        )->map(static fn ($args) => new Path([new CurrentNode(), ...$args]));
    }

    /** @return Parser<ExistsExpression> */
    private static function existsExpression(): Parser
    {
        return self::currentNodeFollowUp()->map(
            static fn (Path $path) => new ExistsExpression($path),
        );
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
            keepFirst(char(','), skipHSpace()),
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
