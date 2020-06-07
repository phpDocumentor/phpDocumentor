<?php declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 * @author Ryan Weaver <ryan@symfonycasts.com> on the original DocBuilder.
 * @author Mike van Riel <me@mikevanriel.com> for adapting this to phpDocumentor.
 */

namespace phpDocumentor\Guides;

use League\Flysystem\FilesystemInterface;

final class BuildContext
{
    private $outputFilesystem;
    private $destinationPath;
    private $template;
    private $cachePath;
    private $enableCache;

    public function __construct(
        FilesystemInterface $output,
        string $destinationPath,
        string $template,
        string $cachePath,
        bool $enableCache = true
    ) {
        $this->outputFilesystem = $output;
        $this->destinationPath = $destinationPath;
        $this->template = $template;
        $this->cachePath = $cachePath;
        $this->enableCache = $enableCache;
    }

    public function getOutputFilesystem() : FilesystemInterface
    {
        return $this->outputFilesystem;
    }

    public function getDestinationPath() : string
    {
        return $this->destinationPath;
    }

    public function getTemplate() : string
    {
        return $this->template;
    }

    public function getCachePath() : string
    {
        return $this->cachePath;
    }

    public function isCacheEnabled() : bool
    {
        return $this->enableCache;
    }
}
