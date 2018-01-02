<?php
/**
 * This file is part of phpDocumentor.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 *  @copyright 2010-2018 Mike van Riel<mike@phpdoc.org>
 *  @license   http://www.opensource.org/licenses/mit-license.php MIT
 *  @link      http://phpdoc.org
 */

namespace phpDocumentor\Infrastructure\Parser;

use phpDocumentor\DomainModel\Dsn;
use phpDocumentor\DomainModel\Parser\FileCollector;
use phpDocumentor\Infrastructure\FlySystemFactory;
use phpDocumentor\Infrastructure\SpecificationFactory;
use phpDocumentor\Reflection\File;

final class FlySystemCollector implements FileCollector
{
    /**
     * @var SpecificationFactory
     */
    private $specificationFactory;
    /**
     * @var FlySystemFactory
     */
    private $flySystemFactory;

    /**
     * FlySystemCollector constructor.
     * @param SpecificationFactory $specificationFactory
     * @param FlySystemFactory $flySystemFactory
     */
    public function __construct(SpecificationFactory $specificationFactory, FlySystemFactory $flySystemFactory)
    {
        $this->specificationFactory = $specificationFactory;
        $this->flySystemFactory = $flySystemFactory;
    }

    public function getFiles(Dsn $dsn, array $paths, array $ignore, array $extensions): array
    {
        $specs = $this->specificationFactory->create($paths, $ignore, $extensions);

        $fileSystems = [];

        $fileSystems[] = $this->flySystemFactory->create($dsn);

        $files = [];
        foreach ($fileSystems as $fileSystem) {
            $result = $fileSystem->find($specs);
            foreach ($result as $file) {
                $files[] = new FlySystemFile($fileSystem, $file['path']);
            }
        }

        return $files;
    }
}
