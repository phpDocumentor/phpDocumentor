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

namespace phpDocumentor\Transformer\Writer;

use League\Uri\UriString;
use phpDocumentor\Transformer\Transformation;

use function ltrim;
use function str_starts_with;
use function strpos;
use function substr;

trait IoTrait
{
    protected function copy(Transformation $transformation, string $path, string $destination): void
    {
        $path = $this->normalizeSourcePath($path);

        $metadata = $transformation->template()->files()->getMetadata($path);
        $type = $metadata ? ($metadata['type'] ?? null) : null;

        if ($type === 'file') {
            $destinationFileSystem = $transformation->getTransformer()->destination();
            if ($destinationFileSystem->has($destination)) {
                $destinationFileSystem->delete($destination);
            }

            $destinationFileSystem->putStream(
                $destination,
                $transformation->template()->files()->readStream($path),
            );

            return;
        }

        $this->copyDirectory($transformation, $path, $destination);
    }

    protected function persistTo(Transformation $transformation, string $path, string $contents): void
    {
        $transformation->getTransformer()->destination()->put($path, $contents);
    }

    private function copyDirectory(Transformation $transformation, string $path, string $destination): void
    {
        $list = $transformation->template()->files()->listContents($path, true);
        $scheme = UriString::parse($path)['scheme'];
        foreach ($list as $file) {
            if ($file['type'] !== 'file') {
                continue;
            }

            // always strip the folder name as we want the path as it is 'inside' the destination folder
            // ex. images/subfolder/image1.png should become subfolder/image1.png as the $destination variable
            // already contains 'images'
            $destinationPath = $this->stripFirstPartOfPath($file['path']);

            // if the provided $path is a reference to a global template, then we need to strip another level
            // since that contains the templateName
            if ($this->isGlobalTemplateReference($path)) {
                $destinationPath = $this->stripFirstPartOfPath($destinationPath);
            }

            $this->copy(
                $transformation,
                $scheme . '://' . $file['path'],
                $destination . '/' . $destinationPath,
            );
        }
    }

    private function stripFirstPartOfPath(string $path): string
    {
        $findPathSeparator = strpos($path, '/', 1);
        if ($findPathSeparator === false) {
            return $path;
        }

        return ltrim(substr($path, $findPathSeparator), '/');
    }

    private function isGlobalTemplateReference(string $path): bool
    {
        return str_starts_with($path, 'templates/')
            || str_starts_with($path, 'templates://');
    }

    private function normalizeSourcePath(string $path): string
    {
        // if it has a scheme, it must have been normalized before
        if (UriString::parse($path)['scheme']) {
            return $path;
        }

        if ($this->isGlobalTemplateReference($path)) {
            // the base folder of the global filesystem is already templates; so we need to strip that off
            return 'templates://' . $this->stripFirstPartOfPath($path);
        }

        return 'template://' . $path;
    }
}
