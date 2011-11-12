<?php
/**
 * DocBlox
 *
 * PHP Version 5
 *
 * @category  DocBlox
 * @package   Tokens
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2011 Mike van Riel / Naenius. (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://docblox-project.org
 */

/**
 * Iterator class responsible for navigating through a list of Tokens.
 *
 * @category DocBlox
 * @package  Tokens
 * @author   Mike van Riel <mike.vanriel@naenius.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     http://docblox-project.org
 */
class DocBlox_Reflection_TokenIterator extends DocBlox_Reflection_TokenIteratorBase
{
    /** @var string|null The name of the file where these tokens come from */
    protected $filename = null;

    /**
     * Initializes the token store.
     *
     * @param string|DocBlox_Reflection_Token[]|string[] $data String to parse
     *      or a list of DocBlox_Tokens, or the result from a token_get_all()
     *      method call.
     *
     * @see token_get_all()
     */
    public function  __construct($data)
    {
        // convert to token objects; converting up front is _faster_ than ad
        // hoc conversion
        foreach ($data as $key => $token) {
            if ($token instanceof DocBlox_Reflection_Token) {
                continue;
            }

            $data[$key] = new DocBlox_Reflection_Token($token);
        }

        parent::__construct($data);
    }

    /**
     * Returns the name of the file where these tokens come from; or null
     * if unknown.
     *
     * @return null|string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Sets the filename where these tokens come from.
     *
     * @param string $filename path to the file which contains these tokens.
     *
     * @return void
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
    }

    /**
     * Finds a token of $type within $max_count tokens in the given $direction,
     * moves the internal pointer when found * and returns the found token,
     * false if none found.
     *
     * @param int      $type      The type of token to find as identified by
     *    the token constants, i.e. T_STRING
     * @param string   $direction The direction where to search, may be
     *    'next' or 'previous'
     * @param int      $max_count The maximum number of tokens to iterate,
     *    0 is unlimited (not recommended)
     * @param string[] $stop_at   Stops searching when one of these token
     *    constants or literals is encountered
     *
     * @throws InvalidArgumentException
     *
     * @return bool|DocBlox_Reflection_Token
     */
    protected function gotoTokenByTypeInDirection(
        $type, $direction = 'next', $max_count = 0, $stop_at = null
    ) {
        // direction must be 'next' or 'previous'
        if (($direction != 'next') && ($direction != 'previous')) {
            throw new InvalidArgumentException(
                'The direction must be a string containing either "next" or '
                . '"previous"'
            );
        }

        if ($max_count < 0) {
            throw new InvalidArgumentException(
                'The maximum count must be a greater or equal to 0'
            );
        }

        $type = is_scalar($type) ? array($type) : $type;

        // initialize basic data
        $token = null;
        $found = false;
        $count = 0;
        $index = $this->key();

        // if $stop_at is a single value, convert to array for ease of parsing
        if (($stop_at !== null) && is_scalar($stop_at)) {
            $stop_at = array($stop_at);
        }

        // loop until we have reached the end
        while (($token = $this->$direction()) !== false) {
            $count++;

            $token_type = $token->type;

            // the direction methods (next() and previous()) return false if
            // the end of the store is encountered
            if ((($max_count > 0) && ($count == $max_count))
                || (($stop_at !== null)
                && (in_array($token_type, $stop_at)
                || in_array($token->content, $stop_at)))
            ) {
                break;
            }

            // break away if we found our token
            if (in_array($token_type, $type)) {
                $found = true;
                break;
            }

        }

        // return to the last known position if none was found
        if (!$found) {
            $this->seek($index);
        }

        // return the result
        return $found ? $token : false;
    }

    /**
     * Finds a token of $type within $max_count tokens in the given $direction
     * and returns the found token, false if none found.
     *
     * Note: this function does _not_ move the internal pointer.
     *
     * @param int      $type      The type of token to find as identified by
     *     the token constants, i.e. T_STRING
     * @param string   $direction The direction where to search, may be
     *     'next' or 'previous'
     * @param int      $max_count The maximum number of tokens to iterate, 0 is
     *     unlimited (not recommended)
     * @param string[] $stop_at   Stops searching when one of these token
     *     constants or literals is encountered
     *
     * @throws InvalidArgumentException
     *
     * @return bool|DocBlox_Reflection_Token
     */
    protected function findByTypeInDirection(
        $type, $direction = 'next', $max_count = 0, $stop_at = null
    ) {
        // store current position
        $index = $this->key();

        // move to token (if found) and get that token
        $found = $this->gotoTokenByTypeInDirection(
            $type, $direction, $max_count, $stop_at
        );

        // return to the last position if the item was found, otherwise the
        // goto method has done the seek for us
        if ($found) {
            $this->seek($index);
        }

        // return the result
        return $found ? $found : false;
    }

    /**
     * Search forward for a token of $type and move the internal pointer when
     * found.
     *
     * @param int      $type      The type of token to find as identified by
     *     the token constants, i.e. T_STRING
     * @param int      $max_count The maximum number of tokens to iterate, 0 is
     *     unlimited (not recommended)
     * @param string[] $stop_at   Stops searching when one of these token
     *     constants or literals is encountered
     *
     * @return bool|DocBlox_Reflection_Token
     */
    public function gotoNextByType($type, $max_count = 0, $stop_at = null)
    {
        return $this->gotoTokenByTypeInDirection(
            $type, 'next', $max_count, $stop_at
        );
    }

    /**
     * Search backward for a token of $type and move the internal pointer when
     * found.
     *
     * @param int      $type      The type of token to find as identified by
     *     the token constants, i.e. T_STRING
     * @param int      $max_count The maximum number of tokens to iterate, 0 is
     *     unlimited (not recommended)
     * @param string[] $stop_at   Stops searching when one of these token
     *     constants or literals is encountered
     *
     * @return bool|DocBlox_Reflection_Token
     */
    public function gotoPreviousByType($type, $max_count = 0, $stop_at = null)
    {
        return $this->gotoTokenByTypeInDirection(
            $type, 'previous', $max_count, $stop_at
        );
    }

    /**
     * Search forward for a token of $type and _not_ move the internal pointer
     * when found.
     *
     * @param int      $type      The type of token to find as identified by
     *     the token constants, i.e. T_STRING
     * @param int      $max_count The maximum number of tokens to iterate, 0 is
     *     unlimited (not recommended)
     * @param string[] $stop_at   Stops searching when one of these token
     *     constants or literals is encountered
     *
     * @return bool|DocBlox_Reflection_Token
     */
    public function findNextByType($type, $max_count = 0, $stop_at = null)
    {
        return $this->findByTypeInDirection($type, 'next', $max_count, $stop_at);
    }

    /**
     * Search backward for a token of $type and _not_ move the internal pointer
     * when found.
     *
     * @param int      $type      The type of token to find as identified by the
     *     token constants, i.e. T_STRING
     * @param int      $max_count The maximum number of tokens to iterate, 0 is
     *     unlimited (not recommended)
     * @param string[] $stop_at   Stops searching when one of these token
     *     constants or literals is encountered
     *
     * @return bool|DocBlox_Reflection_Token
     */
    public function findPreviousByType($type, $max_count = 0, $stop_at = null)
    {
        return $this->findByTypeInDirection(
            $type, 'previous', $max_count, $stop_at
        );
    }

    /**
     * Find the first and last index of a set of matching pair literals
     * (i.e. {}, (), []).
     *
     * @param string $start_literal Opening character, i.e. {
     * @param string $end_literal   Closing character, i.e. }
     *
     * @return int[]
     */
    protected function getTokenIdsBetweenPair($start_literal, $end_literal)
    {
        // store current position
        $index = $this->key();

        // initialize basic variables
        $level = -1;
        $start = null;
        $end = null;

        // iterate through the list until a matching pair is found
        $this->next();
        while ($this->valid()) {
            $token = $this->current();

            // only respond to literals
            if (($token->type !== null)) {
                $this->next();
                continue;
            }

            // if the literal is the same as our starting literal then increase
            // the nesting level
            if ($token->content == $start_literal) {
                // if the nesting level is -1 then we found our opening brace
                if ($level == -1) {
                    // increase the level an additional time because we
                    // started at -1
                    $level++;
                    $start = $this->key();
                }
                $level++;
                $this->next();
                continue;
            } elseif ($token->content == $end_literal) {
                if ($level == -1) {
                    // expect the first brace to be an opening brace
                    break;
                }
                $level--;

                // reached the end!
                if ($level === 0) {
                    $end = $this->key();
                    break;
                }

                $this->next();
                continue;
            }

            $this->next();
        }

        // return to the last position
        $this->seek($index);

        return array(
            $start, $end
        );
    }

    /**
     * Returns the starting and ending position of the next curly braces
     * pair, i.e. {}.
     *
     * @return int[]
     */
    public function getTokenIdsOfBracePair()
    {
        return $this->getTokenIdsBetweenPair('{', '}');
    }

    /**
     * Returns the starting and ending position of the next parenthesis pair,
     * i.e. ().
     *
     * @return int[]
     */
    public function getTokenIdsOfParenthesisPair()
    {
        return $this->getTokenIdsBetweenPair('(', ')');
    }

}