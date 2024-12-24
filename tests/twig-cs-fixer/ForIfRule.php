<?php

declare(strict_types=1);

namespace TwigCsFixer;

use TwigCsFixer\Rules\AbstractFixableRule;
use TwigCsFixer\Token\Token;
use TwigCsFixer\Token\Tokens;

final class ForIfRule extends AbstractFixableRule
{
    protected function process(int $tokenIndex, Tokens $tokens): void
    {
        $token = $tokens->get($tokenIndex);
        if ($token->getValue() !== 'for') {
            return;
        }

        if ($token->getType() !== Token::BLOCK_NAME_TYPE) {
            return;
        }

        $ifTokenIndex = null;
        $fixer = null;
        foreach ($tokens->toArray() as $index => $token) {
            if ($index <= $tokenIndex) {
                continue;
            }

            // We found the end of the for block
            if ($token->isMatching([Token::BLOCK_END_TYPE])) {
                return;
            }

            if ($token->getValue() !== 'if') {
                continue;
            }

            $fixer = $this->addFixableError('If statement inside for loop is not allowed', $token);
            if ($fixer === null) {
                return;
            }

            $ifTokenIndex = $index;

            break;
        }

        if ($fixer === null || $ifTokenIndex === null) {
            return;
        }

        $endBlock = $tokens->findNext(Token::BLOCK_END_TYPE, $tokenIndex);

        $nameIndex = $tokens->findNext(Token::NAME_TYPE, $ifTokenIndex + 1, $endBlock);
        if ($nameIndex === false) {
            return;
        }

        $fixer->beginChangeSet();
        $fixer->addContentBefore($ifTokenIndex, '|');
        $fixer->replaceToken($ifTokenIndex, sprintf(' | filter (%s => ', $tokens->get($nameIndex)->getValue()));
        $fixer->addContentBefore($endBlock, ')');
        $fixer->endChangeSet();
    }
}
