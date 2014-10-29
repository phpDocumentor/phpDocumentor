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

namespace phpDocumentor\Plugin\LegacyNamespaceConverter;

use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use Zend\Filter\AbstractFilter;

/**
 * Converts elements with underscores into a namespaced version.
 *
 * This filter will examine the Name of an element and extract namespaces based on underscores in the
 * name. Every underscore is treated as a namespace separator.
 *
 * @author david0 <https://github.com/david0> this plugin was generously provided by `@david0`.
 * @link   https://github.com/phpDocumentor/phpDocumentor2/pull/1135
 */
class LegacyNamespaceFilter extends AbstractFilter
{
    /** @var ProjectDescriptorBuilder $builder */
    protected $builder;

    /** @var string */
    private $namespacePrefix='';

    /**
     * Initializes this filter with an instance of the builder to retrieve the latest ProjectDescriptor from.
     *
     * @param ProjectDescriptorBuilder $builder
     */
    public function __construct(ProjectDescriptorBuilder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * Overrides the name and namespace of an element with a separated version of the class name.
     *
     * If a class is separated by underscores than the last part is set as name and the first parts are set as
     * namespace with the namespace separator instead of an underscore.
     *
     * @param DescriptorAbstract $value
     *
     * @return DescriptorAbstract|null
     */
    public function filter($value)
    {
        if ($value) {
            $namespace = $value->getNamespace()=='' ? '\\' . $this->namespacePrefix : $value->getNamespace();
            $value->setNamespace($this->namespaceFromLegacyNamespace($namespace, $value->getName()));
            $value->setName($this->classNameFromLegacyNamespace($value->getName()));
        }

        return $value;
    }

    /**
     * Extracts the namespace from the class name.
     *
     * @param string $namespace
     * @param string $className
     *
     * @return string
     */
    private function namespaceFromLegacyNamespace($namespace, $className)
    {
        $qcn = str_replace('_', '\\', $className);

        $lastBackslash = strrpos($qcn, '\\');
        if ($lastBackslash) {
            $namespace = rtrim($namespace, '\\') . '\\' . substr($qcn, 0, $lastBackslash);
        }

        return $namespace;
    }

    /**
     * Extracts the class name without prefix from the full class name.
     *
     * @param string $className
     *
     * @return string
     */
    private function classNameFromLegacyNamespace($className)
    {
        $lastUnderscore = strrpos($className, '_');
        if ($lastUnderscore) {
            $className = substr($className, $lastUnderscore + 1);
        }

        return $className;
    }

    /**
     * Set a prefix for all elements without an namespace
     *
     * @param string $prefix
     */
    public function setNamespacePrefix($prefix)
    {
        $this->namespacePrefix = $prefix;
    }
}
