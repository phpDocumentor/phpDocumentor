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

namespace phpDocumentor\Validation;


final class Violation
{
    private $elementName;
    private $code;
    private $message;
    private $severity;

    /**
     * Violation constructor.
     * @param string $elementName
     * @param $severity
     * @param string $code
     * @param string $message
     */
    public function __construct($elementName, $severity, $code, $message)
    {
        $this->elementName = $elementName;
        $this->code = $code;
        $this->message = $message;
        $this->severity = $severity;
    }

    /**
     * @return string
     */
    public function getElementName()
    {
        return $this->elementName;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return mixed
     */
    public function getSeverity()
    {
        return $this->severity;
    }
}
