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

namespace phpDocumentor\Configuration;

use Doctrine\Common\Annotations\AnnotationReader;

/**
 * Deep-merges any variable.
 *
 * This class is capable of merging together arrays and objects of the same class; all other types of variables are
 * replaced. In addition this merger also supports the `Replace` annotation; this annotation can be placed on a property
 * of a class and will indicate that that property must not be merged but replaced in its entirety.
 */
class Merger
{
    /** @var AnnotationReader Object used to fetch all annotations for structural elements in a given piece of code */
    private $reader;

    /**
     * Initializes this merger with the annotation reader.
     *
     * @param AnnotationReader $reader
     */
    public function __construct(AnnotationReader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * Merges the source on top of the destination and returns the result.
     *
     * @param mixed $destination The destination variable that will be overwritten with the data from the source.
     * @param mixed $source      The source variable that should be merged over the destination.
     * @param mixed $default     For normal variables; only replace that variable if the provided source does
     *     not equal this value.
     *
     * @return mixed the merged variable.
     */
    public function run($destination, $source, $default = null)
    {
        $result = null;

        if (is_object($destination) && is_object($source) && get_class($destination) == get_class($source)) {
            $result = $this->mergeObject($destination, $source);
        } elseif (is_array($source) && is_array($destination)) {
            $result = $this->mergeArray($destination, $source);
        } elseif (!is_null($source) && $source !== $default) {
            $result = $source;
        }

        return $result;
    }

    /**
     * Deep-merge the source object over the destination object and return the results.
     *
     * @param object $destinationObject
     * @param object $sourceObject
     *
     * @return object
     */
    private function mergeObject($destinationObject, $sourceObject)
    {
        $reflectedDestination  = new \ReflectionObject($destinationObject);
        $reflectedSource       = new \ReflectionObject($sourceObject);
        $defaultPropertyValues = $reflectedDestination->getDefaultProperties();

        foreach ($reflectedSource->getProperties() as $sourceProperty) {
            $destinationObject = $this->mergeProperty(
                $destinationObject,
                $reflectedDestination->getProperty($sourceProperty->getName()),
                $sourceObject,
                $sourceProperty,
                $defaultPropertyValues
            );
        }

        return $destinationObject;
    }

    /**
     * Deep-merges the source array over the destination array.
     *
     * @param array $destinationArray
     * @param array $sourceArray
     *
     * @return array
     */
    private function mergeArray($destinationArray, $sourceArray)
    {
        $result = array();
        foreach ($destinationArray as $key => $destinationArrayItem) {
            if (is_int($key)) {
                $result[] = $destinationArrayItem;
            } else {
                $result[$key] = $destinationArrayItem;
            }
        }

        foreach ($sourceArray as $key => $sourceArrayItem) {
            if (is_int($key)) {
                $result[] = $sourceArrayItem;
            } else {
                $result[$key] = $this->run($result[$key], $sourceArrayItem);
            }
        }

        return $result;
    }

    /**
     * Merges the two properties over eachother.
     *
     * @param object              $destinationObject
     * @param \ReflectionProperty $destinationProperty
     * @param object              $sourceObject
     * @param \ReflectionProperty $sourceProperty
     * @param mixed[]             $defaultPropertyValues
     *
     * @return object
     */
    private function mergeProperty(
        $destinationObject,
        \ReflectionProperty $destinationProperty,
        $sourceObject,
        \ReflectionProperty $sourceProperty,
        array $defaultPropertyValues
    ) {
        // Allow the source and destination properties to be readable
        $sourceProperty->setAccessible(true);
        $destinationProperty->setAccessible(true);

        // Retrieve the current value for both the destination and source
        $destinationValue = $destinationProperty->getValue($destinationObject);
        $sourceValue = $sourceProperty->getValue($sourceObject);

        // Find out what the default value for this property is
        $sourcePropertyDefaultValue = isset($defaultPropertyValues[$sourceProperty->getName()])
            ? $defaultPropertyValues[$sourceProperty->getName()]
            : null;

        // if a property is annotated with the 'Replace' annotation then we null the destination location,
        // causing the value from source to be copied as-is to the destination object instead of merging it.
        // but only if the value that is to-be-copied is actually copied
        if ($this->shouldPropertyBeReplaced($destinationProperty)) {
            $destinationValue = null;
        }

        // Merge the values of the two properties!
        $result = $this->run($destinationValue, $sourceValue, $sourcePropertyDefaultValue);

        // No result? No save. We only update the destination if the resulting merge is a value
        if ($result !== null) {
            $destinationProperty->setValue($destinationObject, $result);
        }

        // Make protected and private properties inaccessible again
        $destinationProperty->setAccessible($destinationProperty->isPublic());
        $sourceProperty->setAccessible($sourceProperty->isPublic());

        return $destinationObject;
    }

    /**
     * Tests whether the value of the property should be replaced instead of merged by checking if it has the `Replace`
     * annotation.
     *
     * @param \ReflectionProperty $destinationProperty
     *
     * @return boolean
     */
    private function shouldPropertyBeReplaced(\ReflectionProperty $destinationProperty)
    {
        return (bool) $this->reader->getPropertyAnnotation(
            $destinationProperty,
            'phpDocumentor\Configuration\Merger\Annotation\Replace'
        );
    }
}
