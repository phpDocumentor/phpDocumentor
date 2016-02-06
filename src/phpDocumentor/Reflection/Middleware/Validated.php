<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2015 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Reflection\Middleware;


use League\Event\AbstractEvent;
use phpDocumentor\Validation\Result;

final class Validated extends AbstractEvent
{
    private $path;

    private $validationResult;

    /**
     * Validated constructor.

     * @param Result $validationResult
     */
    public function __construct($path, Result $validationResult)
    {
        $this->path = $path;
        $this->validationResult = $validationResult;
    }

    /**
     * @return string
     */
    public function getValidatedFile()
    {
        return $this->path;
    }

    /**
     * @return Result
     */
    public function getValidationResult()
    {
        return $this->validationResult;
    }
}
