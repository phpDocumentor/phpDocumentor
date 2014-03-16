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
use phpDocumentor\Descriptor\ProjectDescriptor\Settings;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use Zend\Filter\AbstractFilter;

/**
 * Converts legacy namespaces
 */
class LegacyNamespaceFilter extends AbstractFilter
{
    /** @var ProjectDescriptorBuilder $builder */
    protected $builder;


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
     * Will extract the namespace from the Descriptors name 
     *
     * @param DescriptorAbstract $value
     *
     * @return DescriptorAbstract|null
     */
    public function filter($value)
    {
        if($value) {
          $className = $value->getName();
          $value->setNamespace($this->namespaceFromLegacyNamespace($value->getNamespace(), $className));
          $value->setName($this->classNameFromLegacyNamespace($className));

        }
  
        return $value;
    }


    private function namespaceFromLegacyNamespace($namespace, $className)
    {
          $qcn = str_replace('_', '\\', $className);
          if($lastBackslash = strrpos($qcn, '\\')) {
            $namespace = rtrim($namespace, '\\');
            $namespace .= '\\' . substr($qcn, 0, $lastBackslash) ;
          }
          return $namespace;
    }


    private function classNameFromLegacyNamespace($className)
    {
          if($lastUnderscore = strrpos($className, '_'))
              return substr($className, $lastUnderscore+1) ;
          else
              return $className;
    }
}
