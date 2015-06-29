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

namespace phpDocumentor\Application\Commands;

final class LoadProjectFromCache
{
    /** @var string */
    private $source;

    public function __construct($source)
    {
        if (!file_exists($source) || !is_dir($source)) {
            throw new \Exception('Invalid source location provided, a path to an existing folder was expected');
        }

        $this->source = $source;
    }

    /**
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }
}
