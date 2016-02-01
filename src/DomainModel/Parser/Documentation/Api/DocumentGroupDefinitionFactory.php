<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2015 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\DomainModel\Parser\Documentation\Api;

use phpDocumentor\DomainModel\Parser\Documentation\Api\Definition;
use phpDocumentor\DomainModel\Parser\Documentation\DocumentGroup\Definition\Factory as DocumentGroupDefinitionFactoryInterface;
use phpDocumentor\DomainModel\Parser\Documentation\DocumentGroup\DocumentGroupFormat;
use phpDocumentor\DomainModel\Dsn;
use phpDocumentor\Infrastructure\FileSystemFactory;
use phpDocumentor\Infrastructure\SpecificationFactory;

/**
 * Factory for DocumentGroupDefinition.
 * This factory delegates most of it's work to a FileSystemFactory and SpecificationFactory.
 */
final class DocumentGroupDefinitionFactory implements DocumentGroupDefinitionFactoryInterface
{
    /**
     * @var FilesystemFactory
     */
    private $fileSystemFactory;

    /**
     * @var SpecificationFactory
     */
    private $specificationFactory;

    /**
     * Initializes the object with required factories.
     *
     * @param FileSystemFactory $filesystemFactory
     * @param SpecificationFactory $specificationFactory
     */
    public function __construct(FileSystemFactory $filesystemFactory, SpecificationFactory $specificationFactory)
    {
        $this->fileSystemFactory = $filesystemFactory;
        $this->specificationFactory = $specificationFactory;
    }

    /**
     * Creates a Definition using the provided options
     *
     * @param array $options
     *
*@return Definition
     */
    public function create(array $options)
    {
        $format = new DocumentGroupFormat($options['format']);
        $fileSystem = $this->fileSystemFactory->create(new Dsn($options['source']['dsn']));
        $specification = $this->specificationFactory->create(
            $options['source']['paths'],
            $options['ignore'],
            $options['extensions']
        );

        return new Definition($format, $fileSystem, $specification);
    }
}
