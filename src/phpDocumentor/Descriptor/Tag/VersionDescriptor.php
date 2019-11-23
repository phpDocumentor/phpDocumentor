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
 * Descriptor representing the version tag on a class, interface, trait or file.
 */
class VersionDescriptor extends TagDescriptor
{
    /** @var string $version Version string representing the current version of the element */
    protected $version;

    /**
     * Returns the current version for the associated element.
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Sets the version for the associated element.
     *
     * @param string $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }
}
