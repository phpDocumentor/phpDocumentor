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

namespace phpDocumentor;

final class Uri
{
    /**
     * @var string
     */
    private $uri;

    public function __construct($uri)
    {
        $this->uri = (string)$uri;

        $this->validate($uri);
    }

    public function __toString()
    {
        return $this->uri;
    }

    private function validate($uri)
    {
        if (filter_var($uri, FILTER_VALIDATE_URL) === false) {
            throw new \InvalidArgumentException('Invalid uri');
        }
    }
}
