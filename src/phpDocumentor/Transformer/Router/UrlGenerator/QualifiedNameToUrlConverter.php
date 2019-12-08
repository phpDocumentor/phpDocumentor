<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */

namespace phpDocumentor\Transformer\Router\UrlGenerator;

use function ltrim;
use function str_replace;
use function strrpos;
use function strtolower;
use function substr;

/**
 * Service class used to convert Qualified names into URL paths for the Standard Router.
 */
class QualifiedNameToUrlConverter
{
    /**
     * Converts the provided FQCN into a file name by replacing all slashes and underscores with dots.
     */
    public function fromPackage(string $fqcn) : string
    {
        $name = str_replace(['\\', '_'], '-', ltrim($fqcn, '\\'));

        // convert root package to default; default is a keyword and no namespace CAN be named as such
        if ($name === '') {
            $name = 'default';
        }

        return $name;
    }

    /**
     * Converts the provided FQCN into a file name by replacing all slashes with dots.
     */
    public function fromNamespace(string $fqnn) : string
    {
        $name = str_replace('\\', '-', ltrim((string) $fqnn, '\\'));

        // convert root namespace to default; default is a keyword and no namespace CAN be named as such
        if ($name === '') {
            $name = 'default';
        }

        return strtolower($name);
    }

    /**
     * Converts the provided FQCN into a file name by replacing all slashes with dots.
     */
    public function fromClass(string $fqcn) : string
    {
        return str_replace('\\', '-', ltrim((string) $fqcn, '\\'));
    }

    /**
     * Converts the given path to a valid url.
     */
    public function fromFile(string $path) : string
    {
        return str_replace(['/', '\\'], '-', ltrim($path, '/'));
    }
}
