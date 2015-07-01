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

use Symfony\Component\Filesystem\Filesystem;

final class CacheProject
{
    /** @var string */
    private $target;

    public function __construct($target)
    {
        $filesystem = new Filesystem();
        if (! $filesystem->isAbsolutePath($target)) {
            $target = getcwd() . DIRECTORY_SEPARATOR . $target;
        }
        if (!file_exists($target)) {
            @mkdir($target);
        }

        $this->target = $target;
    }

    /**
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }
}
