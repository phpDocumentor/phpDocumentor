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

namespace phpDocumentor\Descriptor\Builder\Reflector;

use phpDocumentor\Descriptor\ConstantDescriptor;
use phpDocumentor\Reflection\Php\Constant;

/**
 * Assembles a ConstantDescriptor from a ConstantReflector.
 */
class ConstantAssembler extends AssemblerAbstract
{
    const SEPARATOR_SIZE = 2;

    /**
     * Creates a Descriptor from the provided data.
     *
     * @param Constant $data
     *
     * @return ConstantDescriptor
     */
    public function create($data)
    {
        $constantDescriptor = new ConstantDescriptor();
        $constantDescriptor->setName($data->getName());
        $constantDescriptor->setValue($data->getValue());
        // Reflection library formulates namespace as global but this is not wanted for phpDocumentor itself
        $constantDescriptor->setNamespace(
            substr((string) $data->getFqsen(), 0, - strlen($data->getName()) - static::SEPARATOR_SIZE)
        );
        $constantDescriptor->setFullyQualifiedStructuralElementName($data->getFqsen());

        $this->assembleDocBlock($data->getDocBlock(), $constantDescriptor);

        $constantDescriptor->setLine($data->getLocation()->getLineNumber());

        return $constantDescriptor;
    }
}
