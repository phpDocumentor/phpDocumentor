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

namespace phpDocumentor\Validation\Rule;

abstract class AbstractRule implements Rule
{
    /**
     * @var int
     */
    protected $severity;

    /**
     * @var string
     */
    protected $messageTemplate;

    /**
     * AbstractRule constructor.
     * @param int $severity
     * @param string $messageTemplate
     */
    public function __construct($severity = Rule::SEVERITY_ERROR, $messageTemplate = null)
    {
        $this->severity = $severity;
        $this->messageTemplate = $messageTemplate;
    }
}
