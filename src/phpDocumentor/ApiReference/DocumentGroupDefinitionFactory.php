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

namespace phpDocumentor\ApiReference;

use phpDocumentor\DocumentGroupDefinitionFactory as DocumentGroupDefinitionFactoryInterface;
use phpDocumentor\DocumentGroupFormat;
use phpDocumentor\Dsn;
use phpDocumentor\Filesystem\FilesystemFactory;
use phpDocumentor\SpecificationFactory;

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

    public function __construct(FilesystemFactory $filesystemFactory, SpecificationFactory $specificationFactory)
    {
        $this->fileSystemFactory = $filesystemFactory;
        $this->specificationFactory = $specificationFactory;
    }

    /**
     * Creates a Definition using the provided options
     *
     * ```
     * 'api' => array(
     *   'format' => 'php',
     *   'source' => array(
     *       'dsn' => 'file://.',
     *       'paths' => array(
     *           0 => 'src'
     *       )
     *   ),
     *   'ignore' => array(
     *   'hidden' => true,
     *   'symlinks' => true,
     *   'paths' => array(
     *   0 => 'src/ServiceDefinitions.php'
     *   )
     *   ),
     *   'extensions' => array(
     *   0 => 'php',
     *   1 => 'php3',
     *   2 => 'phtml'
     *   ),
     *   'visibility' => 'public',
     *   'default-package-name' => 'Default',
     *   'markers' => array(
     *   0 => 'TODO',
     *   1 => 'FIXME'
     *   )
     *   ),
     *
     * @param array $options
     * @return DocumentGroupDefinition
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

        return new DocumentGroupDefinition($format, $fileSystem, $specification);
    }
}

