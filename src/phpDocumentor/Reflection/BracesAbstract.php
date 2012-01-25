<?php
/**
 * phpDocumentor
 *
 * PHP Version 5
 *
 * @category  phpDocumentor
 * @package   Reflection
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

/**
 * Base class for any structure that has a code block (thus followed by {}).
 *
 * @category phpDocumentor
 * @package  Reflection
 * @author   Mike van Riel <mike.vanriel@naenius.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     http://phpdoc.org
 */
abstract class phpDocumentor_Reflection_BracesAbstract
    extends phpDocumentor_Reflection_DocBlockedAbstract
{
    /**
     * Generic method which iterates through all tokens between the braces
     * following the current position in the token iterator.
     *
     * Please note: This method will also move the cursor position in the token
     * iterator forward.
     *
     * When a token is encountered this method will invoke the processToken
     * method, which is defined in the * phpDocumentor_Reflection_Abstract class.
     *
     * Literals are ignored.
     *
     * @param phpDocumentor_Reflection_TokenIterator $tokens Tokens to interpret with
     *     the pointer at the token to be processed.
     *
     * @see phpDocumentor_Reflection_Abstract
     *
     * @return int[]
     */
    public function processTokens(phpDocumentor_Reflection_TokenIterator $tokens)
    {
        $level = -1;
        $start = 0;
        $end = 0;
        $token = null;

        // parse class contents
        $this->debug('>> Processing tokens');
        while ($tokens->valid()) {
            /** @var phpDocumentor_Reflection_Token $token */
            $token = $token === null ? $tokens->current() : $tokens->next();

            $token_type = false;
            $token_content = false;
            if ($token instanceof phpDocumentor_Reflection_Token) {
                $token_type = $token->type;
                $token_content = $token->content;
            }

            // if we encounter a semi-colon before we have an opening brace then
            // this is an abstract or interface function which have no body;
            // stop looking!
            if (($token_type === null)
                && ($token_content === ';')
                && ($level === -1)
            ) {
                return array($start, $end);
            }

            if (($token_type == T_CURLY_OPEN)
                || ($token_type == T_DOLLAR_OPEN_CURLY_BRACES)
            ) {
                $token_content = '{';
            }

            // determine where the 'braced' section starts and end.
            // the first open brace encountered is considered the opening brace
            // for the block and processing will be 'breaked' when the closing
            // brace is encountered
            if ((!$token_type
                || ($token_type == T_CURLY_OPEN)
                || ($token_type == T_DOLLAR_OPEN_CURLY_BRACES))
                && (($token_content == '{') || (($token_content == '}')))
            ) {
                switch ($token_content) {
                case '{':
                    // expect the first brace to be an opening brace
                    if ($level == -1) {
                        $level++;
                        $start = $tokens->key();
                    }
                    $level++;
                    break;
                case '}':
                    if ($level == -1) {
                        continue;
                    }
                    $level--;

                    // reached the end; break from the while
                    if ($level === 0) {
                        $end = $tokens->key();
                        break 2; // time to say goodbye
                    }
                    break;
                }
                continue;
            }

            if ($token && $token_type) {
                // if a token is encountered and it is not a literal,
                // invoke the processToken method
                $this->processToken($token, $tokens);
            }
        }

        // return the start and end token index
        return array($start, $end);
    }

}