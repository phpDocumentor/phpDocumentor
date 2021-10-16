<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Span;

use Doctrine\Common\Lexer\AbstractLexer;
use ReflectionClass;

use function array_column;
use function array_flip;
use function preg_match;

final class SpanLexer extends AbstractLexer
{
    public const WORD = 1;
    public const UNDERSCORE = 2;
    public const ANONYMOUS_END = 3;
    public const PHRASE_ANONYMOUS_END = 4;
    public const LITERAL = 5;
    public const BACKTICK = 6;
    public const NAMED_REFERENCE_END = 7;
    public const INTERNAL_REFERENCE_START = 8;
    public const EMBEDED_URL_START = 9;
    public const EMBEDED_URL_END = 10;
    public const NAMED_REFERENCE = 11;
    public const ANONYMOUSE_REFERENCE = 12;

    /**
     * Map between string position and position in token list.
     *
     * @link https://github.com/doctrine/lexer/issues/53
     *
     * @var array<int, int>
     */
    protected $tokenPositions;

    /** @return string[] */
    protected function getCatchablePatterns()
    {
        return [
            '[a-z0-9-]+_{2}', //Inline href.
            '[a-z0-9-]+_{1}', //Inline href.
            '`_',
            '<',
            '>',
            '\\\\_', // Escaping hell... needs escaped slash in regex, but also in php.
            '_`',
            '`',
            '`__',
            '_{2}',
        ];
    }

    /** @param int $position */
    public function resetPosition($position = 0)
    {
        parent::resetPosition($this->tokenPositions[$position]);
    }

    /** @param string $input */
    protected function scan($input)
    {
        parent::scan($input);

        $class = new ReflectionClass(AbstractLexer::class);
        $property = $class->getProperty('tokens');
        $property->setAccessible(true);
        $tokens = $property->getValue($this);

        $this->tokenPositions = array_flip(array_column($tokens, 'position'));
    }

    /** @return string[] */
    protected function getNonCatchablePatterns()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    protected function getType(&$value)
    {
        if (preg_match('/[a-z0-9-]+_{2}/', $value)) {
            return self::ANONYMOUSE_REFERENCE;
        }

        if (preg_match('/[a-z0-9-]+_{1}/', $value)) {
            return self::NAMED_REFERENCE;
        }

        switch ($value) {
            case '`':
                return self::BACKTICK;

            case '\_':
                $value = '_';
                break;
            case '<':
                return self::EMBEDED_URL_START;

            case '>':
                return self::EMBEDED_URL_END;

            case '_':
                return self::UNDERSCORE;

            case '`_':
                return self::NAMED_REFERENCE_END;

            case '_`':
                return self::INTERNAL_REFERENCE_START;

            case '__':
                return self::ANONYMOUS_END;

            case '`__':
                return self::PHRASE_ANONYMOUS_END;

            default:
                return self::WORD;
        }

        return null;
    }
}
