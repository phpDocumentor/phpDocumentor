<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Plugin\Standards;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\ValidatorInterface;

/**
 * A collection of Sniffs that are available to be activated by a Rule.
 *
 * This collection of sniffs is not enabled by default, which means that these sniffs lie dormant until a Ruleset
 * enables them using the {@see self::enable()} method.
 *
 * @see Ruleset for more information on how the Documentation Standards System works.
 */
class Collection extends \ArrayObject
{
    /**
     * Registers a Sniff with this collection.
     *
     * @param AbstractSniff $sniff
     *
     * @return void
     */
    public function addSniff(AbstractSniff $sniff)
    {
        $this[$sniff->getName()] = $sniff;
    }

    /**
     * Registers a sniff with this collection using the array accessor.
     *
     * Please note that the $index argument is in fact ignored as the index for this sniff is determined by the name in
     * the sniff object.
     *
     * @param string $index [unused]
     * @param AbstractSniff $newval
     *
     * @return void
     */
    public function offsetSet($index, $newval)
    {
        parent::offsetSet($newval->getName(), $newval);
    }

    /**
     * Enables a sniff using its (rule)name and returns whether it succeeded.
     *
     * @param string $name
     *
     * @return boolean
     */
    public function enable($name)
    {
        if (! isset($this[$name])) {
            return false;
        }

        /** @var AbstractSniff $sniff */
        $sniff = $this[$name];
        $sniff->enable();

        return true;
    }

    /**
     * Enables all Sniffs in this collection.
     *
     * @return void
     */
    public function enableAll()
    {
        foreach (array_keys($this->getArrayCopy()) as $key) {
            $this->enable($key);
        }
    }
}
