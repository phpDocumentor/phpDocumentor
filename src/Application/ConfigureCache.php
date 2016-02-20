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

namespace phpDocumentor\Application;

use phpDocumentor\DomainModel\Path;

final class ConfigureCache
{
    /** @var Path */
    private $location;

    /** @var bool */
    private $enabled = true;

    /**
     * @param Path $location
     * @param bool $enabled
     */
    public function __construct(Path $location, $enabled = true)
    {
        $this->location = $location;
        $this->enabled = $enabled;
    }

    /**
     * @return Path
     */
    public function location()
    {
        return $this->location;
    }

    /**
     * @return boolean
     */
    public function enabled()
    {
        return $this->enabled;
    }
}
