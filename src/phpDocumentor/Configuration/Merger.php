<?php

namespace phpDocumentor\Configuration;

use Doctrine\Common\Annotations\AnnotationReader;

class Merger
{
    /**
     * @var AnnotationReader
     */
    private $reader;

    public function __construct(AnnotationReader $reader)
    {
        $this->reader = $reader;
    }

    public function run($destination, $source, $default = null)
    {
        $result = null;

        if (is_object($destination) && is_object($source)) {
            $result = $this->mergeObject($destination, $source);
        } elseif (is_array($source) && is_array($destination)) {
            $result = $this->mergeArray($destination, $source);
        } elseif (!is_null($source) && $source !== $default) {
            $result = $source;
        }

        return $result;
    }

    private function mergeObject($destinationObject, $sourceObject)
    {
        $reflectedDestination  = new \ReflectionObject($destinationObject);
        $reflectedSource       = new \ReflectionObject($sourceObject);
        $defaultPropertyValues = $reflectedDestination->getDefaultProperties();

        foreach($reflectedSource->getProperties() as $sourceProperty) {
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
        return (bool)$this->reader->getPropertyAnnotation(
            $destinationProperty,
            'phpDocumentor\Configuration\Merger\Annotation\Replace'
        );
    }
} 