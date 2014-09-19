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

namespace phpDocumentor\Descriptor\Validator;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\ValidatorInterface;

class Collection extends \ArrayObject
{
    private $mapElementTypeToDescriptorForMetaData = array(
        'File'      => 'phpDocumentor\Descriptor\FileDescriptor',
        'Class'     => 'phpDocumentor\Descriptor\ClassDescriptor',
        'Interface' => 'phpDocumentor\Descriptor\InterfaceDescriptor',
        'Trait'     => 'phpDocumentor\Descriptor\TraitDescriptor',
        'Method'    => 'phpDocumentor\Descriptor\MethodDescriptor',
        'Function'  => 'phpDocumentor\Descriptor\FunctionDescriptor',
        'Property'  => 'phpDocumentor\Descriptor\PropertyDescriptor',
        'Constant'  => 'phpDocumentor\Descriptor\ConstantDescriptor',
    );

    /** @var ValidatorInterface */
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function enable($validatorKey)
    {
        if (! isset($this[$validatorKey])) {
            return false;
        }

        $metaData = null;
        foreach ($this->mapElementTypeToDescriptorForMetaData as $type => $className) {
            if (strpos($validatorKey, $type . '.') === 0) {
                $metaData = $this->validator->getMetadataFor($className);
            }
        }

        $function = $this[$validatorKey];
        $function($this->validator, $metaData);

        return true;
    }

    public function enableAll()
    {
        foreach (array_keys($this->getArrayCopy()) as $key) {
            $this->enable($key);
        }
    }
}
