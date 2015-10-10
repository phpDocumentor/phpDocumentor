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


use Flyfinder\Specification\SpecificationInterface;
use League\Flysystem\FilesystemInterface;
use phpDocumentor\DocumentGroupDefinition as DocumentGroupDefinitionInterface;
use phpDocumentor\DocumentGroupFormat;

/**
 * Document group definition for Api documentation.
 */
final class DocumentGroupDefinition implements DocumentGroupDefinitionInterface
{
    /**
     * @var DocumentGroupFormat
     */
    private $format;

    /**
     * @var FilesystemInterface
     */
    private $filesystem;

    /**
     * @var SpecificationInterface
     */
    private $specification;

    /**
     * Initializes the object with the required format, filesystem and specification to query the filesystem.
     *
     * @param DocumentGroupFormat $format
     * @param FilesystemInterface $filesystem
     * @param SpecificationInterface $specification
     */
    public function __construct(
        DocumentGroupFormat $format,
        FilesystemInterface $filesystem,
        SpecificationInterface $specification
    )
    {
        $this->format = $format;
        $this->filesystem = $filesystem;
        $this->specification = $specification;
    }

    /**
     * Returns the defined format.
     *
     * @return DocumentGroupFormat
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * Returns the internal filesystem
     *
     * @return FilesystemInterface
     */
    public function getFilesystem()
    {
        return $this->filesystem;
    }

    /**
     * Returns an array of paths to files to process.
     *
     * @return string[]
     */
    public function getFiles()
    {
        $files = [];
        $result = $this->filesystem->find($this->specification);
        foreach ($result as $file) {
            $files[] = $file['path'];
        }

        return $files;
    }
}
