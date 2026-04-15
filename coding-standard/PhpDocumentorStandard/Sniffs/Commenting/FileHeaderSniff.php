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

namespace PhpDocumentorStandard\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

use function in_array;
use function preg_replace;

use const T_ATTRIBUTE;
use const T_COMMENT;
use const T_DECLARE;
use const T_DOC_COMMENT_OPEN_TAG;
use const T_NAMESPACE;
use const T_OPEN_TAG;
use const T_SEMICOLON;
use const T_USE;
use const T_WHITESPACE;

final class FileHeaderSniff implements Sniff
{
    private const MISSING = 'Missing';
    private const INVALID = 'Invalid';

    private const HEADER = <<<'EOT'
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */
EOT;

    /** @return array<int|string> */
    public function register(): array
    {
        return [T_OPEN_TAG];
    }

    /**
     * @param File $phpcsFile The file being scanned.
     * @param int  $stackPtr  The position of the current token in the stack.
     */
    public function process(File $phpcsFile, $stackPtr): int
    {
        $tokens = $phpcsFile->getTokens();

        $insertAfter = $stackPtr;
        $next = $this->skipWhitespaceAndComments($tokens, $stackPtr + 1, $insertAfter);

        while ($next !== false && $tokens[$next]['code'] === T_DECLARE) {
            $closer = $tokens[$next]['parenthesis_closer'] ?? null;
            if ($closer === null) {
                break;
            }

            $i = $closer + 1;
            while (isset($tokens[$i]) && $tokens[$i]['code'] === T_WHITESPACE) {
                $i++;
            }

            if (! isset($tokens[$i]) || $tokens[$i]['code'] !== T_SEMICOLON) {
                break;
            }

            $insertAfter = $i;
            $next = $this->skipWhitespaceAndComments($tokens, $i + 1, $insertAfter);
        }

        if ($next !== false && $tokens[$next]['code'] === T_DOC_COMMENT_OPEN_TAG) {
            $end = $tokens[$next]['comment_closer'];
            $actual = $phpcsFile->getTokensAsString($next, $end - $next + 1);
            $normalized = preg_replace('/\r\n?/', "\n", $actual) ?? $actual;

            if ($normalized === self::HEADER) {
                return $phpcsFile->numTokens;
            }

            $afterDocBlock = $phpcsFile->findNext([T_WHITESPACE], $end + 1, null, true);

            while (
                $afterDocBlock !== false
                && $tokens[$afterDocBlock]['code'] === T_ATTRIBUTE
                && isset($tokens[$afterDocBlock]['attribute_closer'])
            ) {
                $afterDocBlock = $phpcsFile->findNext(
                    [T_WHITESPACE],
                    $tokens[$afterDocBlock]['attribute_closer'] + 1,
                    null,
                    true,
                );
            }

            $isFileLevelByPosition = $afterDocBlock === false || in_array(
                $tokens[$afterDocBlock]['code'],
                [T_NAMESPACE, T_USE, T_DECLARE, T_DOC_COMMENT_OPEN_TAG],
                true,
            );

            if ($isFileLevelByPosition) {
                $fix = $phpcsFile->addFixableError(
                    'Invalid file-level DocBlock; must match the canonical phpDocumentor header. '
                    . 'Any existing content in this DocBlock will be replaced when auto-fixing.',
                    $next,
                    self::INVALID,
                );

                if ($fix === true) {
                    $phpcsFile->fixer->beginChangeset();
                    for ($i = $next + 1; $i <= $end; $i++) {
                        $phpcsFile->fixer->replaceToken($i, '');
                    }

                    $phpcsFile->fixer->replaceToken($next, self::HEADER);
                    $phpcsFile->fixer->endChangeset();
                }

                return $phpcsFile->numTokens;
            }
        }

        $fix = $phpcsFile->addFixableError(
            'Missing file-level DocBlock; expected the canonical phpDocumentor header.',
            $insertAfter,
            self::MISSING,
        );

        if ($fix === true) {
            $phpcsFile->fixer->beginChangeset();
            $phpcsFile->fixer->addContent($insertAfter, "\n\n" . self::HEADER);
            $phpcsFile->fixer->endChangeset();
        }

        return $phpcsFile->numTokens;
    }

    /**
     * Walks forward past whitespace and line/block comments, tracking the last
     * comment as the preferred insertion anchor so the header lands below any
     * stray leading comments rather than above them.
     *
     * @param array<int, array<string, mixed>> $tokens
     *
     * @return int|false Index of the first non-skippable token, or false at end of file.
     */
    private function skipWhitespaceAndComments(array $tokens, int $start, int &$insertAfter)
    {
        $i = $start;
        while (isset($tokens[$i])) {
            $code = $tokens[$i]['code'];
            if ($code === T_WHITESPACE) {
                $i++;
                continue;
            }

            if ($code === T_COMMENT) {
                $insertAfter = $i;
                $i++;
                continue;
            }

            return $i;
        }

        return false;
    }
}
