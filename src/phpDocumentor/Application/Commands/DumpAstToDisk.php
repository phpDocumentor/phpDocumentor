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

use Webmozart\Assert\Assert;

/**
 * Command used to dump a serialized version of the project to disk for debugging purposes.
 */
final class DumpAstToDisk
{
    /** @var string */
    private $location;

    /**
     * Registers the location where the AST should be dumped to.
     *
     * @param string $location
     */
    public function __construct($location)
    {
        Assert::stringNotEmpty($location);

        $this->location = $location;
    }

    /**
     * Returns the location where the ast should be written to.
     *
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }
}
