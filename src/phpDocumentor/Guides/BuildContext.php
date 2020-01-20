<?php declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 * @author Ryan Weaver <ryan@symfonycasts.com> on the original DocBuilder.
 * @author Mike van Riel <me@mikevanriel.com> for adapting this to phpDocumentor.
 */

namespace phpDocumentor\Guides;

use Doctrine\RST\Configuration;
use Exception;
use League\Flysystem\FilesystemInterface;
use LogicException;

class BuildContext
{
    private $symfonyApiUrl;
    private $phpDocUrl;
    private $symfonyDocUrl;

    private $runtimeInitialized = false;
    private $sourceDir;
    private $outputFilesystem;
    private $parseSubPath;
    private $disableCache = false;

    public function __construct(
        string $symfonyApiUrl,
        string $phpDocUrl,
        string $symfonyDocUrl
    ) {
        $this->symfonyApiUrl = $symfonyApiUrl;
        $this->phpDocUrl = $phpDocUrl;
        $this->symfonyDocUrl = $symfonyDocUrl;
    }

    public function initializeRuntimeConfig(
        string $sourceDir,
        FilesystemInterface $output,
        ?string $parseSubPath = null,
        bool $disableCache = false
    ) {
        if (!file_exists($sourceDir)) {
            throw new Exception(sprintf('Source directory "%s" does not exist', $sourceDir));
        }

        $this->sourceDir = realpath($sourceDir);
        $this->outputFilesystem = $output;
        $this->parseSubPath = $parseSubPath;
        $this->disableCache = $disableCache;
        $this->runtimeInitialized = true;
    }

    public function getSymfonyApiUrl() : string
    {
        return $this->symfonyApiUrl;
    }

    public function getPhpDocUrl() : string
    {
        return $this->phpDocUrl;
    }

    public function getSymfonyDocUrl() : string
    {
        return $this->symfonyDocUrl;
    }

    public function getSourceDir() : string
    {
        $this->checkThatRuntimeConfigIsInitialized();

        return $this->sourceDir;
    }

    public function getOutputFilesystem() : FilesystemInterface
    {
        $this->checkThatRuntimeConfigIsInitialized();

        return $this->outputFilesystem;
    }

    public function getParseSubPath() : ?string
    {
        $this->checkThatRuntimeConfigIsInitialized();

        return $this->parseSubPath;
    }

    public function getDisableCache() : bool
    {
        $this->checkThatRuntimeConfigIsInitialized();

        return $this->disableCache;
    }

    private function checkThatRuntimeConfigIsInitialized()
    {
        if (false === $this->runtimeInitialized) {
            throw new LogicException('The BuildContext has not been initialized');
        }
    }
}
