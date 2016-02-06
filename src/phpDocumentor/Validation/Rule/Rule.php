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

use phpDocumentor\Validation\Result;

interface Rule
{
    const SEVERITY_ERROR = 3;

    const SEVERITY_WARNING = 2;

    const SEVERITY_NOTICE = 1;

    public function validate($element, Result $result);
}
