<?php
/**
 *  This file is part of phpDocumentor.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 *  @copyright 2010-${YEAR} Mike van Riel<mike@phpdoc.org>
 *  @license   http://www.opensource.org/licenses/mit-license.php MIT
 *  @link      https://phpdoc.org
 */


/**
 * Lorem ipsum dolor sit amet, consectetur adipisicing elit.
 *
 * Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
 * tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
 * quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
 * consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
 * cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat
 * non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
 *
 * @param  integer $a A random number.
 * @param  string  $b A random text.
 * @param  boolean $c A random boolean.
 * @return string     All the parameters concatenated.
 */
function foo($a = 0, $b = '', $c = false)
{
    return sprintf(
        'a: %d, b: %s, c: %s',
        $a, /* default to zero */
        $b, /* default to empty */
        ($c ? 'True' : 'False')
    );
}
