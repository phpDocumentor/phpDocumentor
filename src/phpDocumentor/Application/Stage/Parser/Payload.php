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

namespace phpDocumentor\Application\Stage\Parser;

use phpDocumentor\Application\Stage\Payload as ApplicationPayload;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use phpDocumentor\Reflection\File;

final class Payload extends ApplicationPayload
{
    /**
     * @var File[]
     */
    private $files;

    /**
     * @param File[] $files
     */
    public function __construct(array $config, ProjectDescriptorBuilder $builder, array $files = [])
    {
        parent::__construct($config, $builder);
        $this->files = $files;
    }

    public function getApiConfig()
    {
        //Grep only the first version for now. Multi version support will be added later
        $version = current($this->getConfig()['phpdocumentor']['versions']);

        //We are currently in the parser stage so grep the api config.
        //And for now we support a single api definition. Could be more in the future.
        return $version['api'][0];
    }

    public function withFiles(array $files) : Payload
    {
        return new self($this->getConfig(), $this->getBuilder(), $files);
    }

    /**
     * @return File[]
     */
    public function getFiles() : array
    {
        return $this->files;
    }
}
