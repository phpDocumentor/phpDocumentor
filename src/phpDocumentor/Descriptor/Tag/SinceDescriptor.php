<?php
declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Tag;

use phpDocumentor\Descriptor\TagDescriptor;

/**
 * Descriptor representing the since tag with another descriptor.
 */
class SinceDescriptor extends TagDescriptor
{
    /** @var string $version represents the version since when the associated element was introduced */
    protected $version;

    /**
     * Returns the version when the associated element was introduced.
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Sets the version since when the associated element was introduced.
     *
     * @param string $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }
}
