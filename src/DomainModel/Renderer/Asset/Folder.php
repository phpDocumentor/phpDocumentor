<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2016 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\DomainModel\Renderer\Asset;

use phpDocumentor\DomainModel\Path;
use Webmozart\Assert\Assert;

class Folder extends \ArrayObject
{
    /**
     * @var Path
     */
    private $path;

    /**
     * @param Path $path
     * @param Path[] $locations
     */
    public function __construct(Path $path, array $locations)
    {
        Assert::allIsInstanceOf($locations, Path::class);

        $this->path = $path;
        parent::__construct($locations);
    }

    /**
     * @return Path
     */
    public function path()
    {
        return $this->path;
    }

    /**
     * @param int|string $index
     * @param Path $newval
     */
    public function offsetSet($index, $newval)
    {
        Assert::isInstanceOf($newval, Path::class);

        parent::offsetSet($index, $newval);
    }
}
