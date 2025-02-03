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

use phpDocumentor\Transformer\Transformation;

use function ltrim;
use function strpos;
use function substr;

trait IoTrait
{
    protected function copy(Transformation $transformation, string $path, string $destination): void
    {
        if ($transformation->template()->files()->has($path)) {
            $transformation->getTransformer()->destination()->put(
                $destination,
                $transformation->template()->files()->read($path),
            );
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
        foreach ($list as $file) {
            if ($file['type'] !== 'file') {
                continue;
            }

            // always strip the folder name as we want the path as it is 'inside' the destination folder
            // ex. images/subfolder/image1.png should become subfolder/image1.png as the $destination variable
            // already contains 'images'
            $destinationPath = $this->stripFirstPartOfPath($file['path']);

            $this->copy(
                $transformation,
                $file['path'],
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
}
