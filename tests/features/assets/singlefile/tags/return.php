<?php
/**
 * This file is part of phpDocumentor.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 * @copyright 2010-2018 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

final class ReturnTag
{
    public function get(): int
    {
    }

    /**
     * @return string description
     */
    public function getReturnWithDefinedReturn(): string
    {
    }

    public function getReturnWithoutAny()
    {
    }

    /**
     * @return string some value
     */
    public function getReturnDescription()
    {
    }

    /**
     * @return (integer|string)[]
     */
    public function getMultiTypeArray()
    {
    }
}

function get(): int
{
}

/**
 * @return string description
 */
function getReturnWithDefinedReturn(): string
{
}

function getReturnWithoutAny()
{
}

/**
 * @return string some value
 */
function getReturnDescription()
{
}
