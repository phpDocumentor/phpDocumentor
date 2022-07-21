<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor\Pipeline\Stage\Parser;

use phpDocumentor\Configuration\ApiSpecification;
use phpDocumentor\Configuration\Configuration;
use phpDocumentor\Configuration\VersionSpecification;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use phpDocumentor\Pipeline\Stage\Payload as ApplicationPayload;
use phpDocumentor\Reflection\File;
use Webmozart\Assert\Assert;

use function array_merge;
use function current;

/**
 * @psalm-import-type ConfigurationMap from Configuration
 */
final class Payload extends ApplicationPayload
{
    /** @var File[] */
    private array $files;

    /**
     * @param ConfigurationMap $config
     * @param File[] $files
     */
    public function __construct(array $config, ProjectDescriptorBuilder $builder, array $files = [])
    {
        parent::__construct($config, $builder);
        $this->files = $files;
    }

    /**
     * @return array<int, ApiSpecification>
     */
    public function getApiConfigs(): array
    {
        // Grep only the first version for now. Multi version support will be added later
        $version = current($this->getConfig()['phpdocumentor']['versions']);
        Assert::isInstanceOf($version, VersionSpecification::class);

        return $version->getApi();
    }

    /**
     * @param array<File> $files
     */
    public function withFiles(array $files): Payload
    {
        return new self(
            $this->getConfig(),
            $this->getBuilder(),
            array_merge($this->getFiles(), $files)
        );
    }

    /**
     * @return File[]
     */
    public function getFiles(): array
    {
        return $this->files;
    }
}
