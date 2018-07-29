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

namespace phpDocumentor\Descriptor\ProjectDescriptor;

/**
 * Contains the Settings for the current Project.
 */
class Settings
{
    const VISIBILITY_PUBLIC = 1;

    const VISIBILITY_PROTECTED = 2;

    const VISIBILITY_PRIVATE = 4;

    const VISIBILITY_INTERNAL = 8;

    /** @var integer by default ignore internal visibility but show others */
    const VISIBILITY_DEFAULT = 7;

    /** @var boolean Represents whether this settings object has been modified */
    protected $isModified = false;

    /** @var integer a bitflag representing which visibilities are contained and allowed in this project */
    protected $visibility = self::VISIBILITY_DEFAULT;

    /** @var bool */
    protected $includeSource = false;

    private $markers;

    /**
     * Stores the visibilities that are allowed to be executed as a bitflag.
     *
     * @param integer $visibilityFlag A bitflag combining the VISIBILITY_* constants.
     */
    public function setVisibility($visibilityFlag)
    {
        $this->setValueAndCheckIfModified('visibility', $visibilityFlag);
    }

    /**
     * Returns the bit flag representing which visibilities are allowed.
     *
     * @see self::isVisibilityAllowed() for a convenience method to easily check against a specific visibility.
     *
     * @return integer
     */
    public function getVisibility()
    {
        return $this->visibility;
    }

    /**
     * Returns whether one of the values of this object was modified.
     *
     * @return boolean
     */
    public function isModified()
    {
        return $this->isModified;
    }

    /**
     * Resets the flag indicating whether the settings have changed.
     */
    public function clearModifiedFlag()
    {
        $this->isModified = false;
    }

    /**
     * Sets a property's value and if it differs from the previous then mark these settings as modified.
     */
    protected function setValueAndCheckIfModified($propertyName, $value)
    {
        if ($this->{$propertyName} !== $value) {
            $this->isModified = true;
        }

        $this->{$propertyName} = $value;
    }

    public function includeSource()
    {
        $this->includeSource = true;
    }

    public function excludeSource()
    {
        $this->includeSource = false;
    }

    public function shouldIncludeSource()
    {
        return $this->includeSource;
    }

    public function setMarkers(array $markers)
    {
        $this->markers = $markers;
    }

    public function getMarkers()
    {
        return $this->markers;
    }
}
