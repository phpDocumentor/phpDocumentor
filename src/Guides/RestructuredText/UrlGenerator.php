<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText;

use function array_pop;
use function basename;
use function count;
use function explode;
use function implode;
use function ltrim;
use function preg_match;
use function rtrim;
use function strpos;
use function substr;

class UrlGenerator
{
    /** @var Configuration */
    private $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function generateUrl(string $path, string $currentFileName, string $dirName) : string
    {
        $canonicalPath = (string) $this->canonicalUrl($dirName, $path);

        if ($this->configuration->isBaseUrlEnabled($canonicalPath)) {
            $baseUrl = $this->configuration->getBaseUrl();

            return rtrim($baseUrl, '/') . '/' . ltrim($canonicalPath, '/');
        }

        return (string) $this->relativeUrl($path, $currentFileName);
    }

    public function absoluteUrl(string $dirName, string $url) : string
    {
        // if $url is already an absolute path, just return it
        if ($url[0] === '/') {
            return $url;
        }

        // $url is a relative path so join it together with the
        // current $dirName to produce an absolute url
        return rtrim($dirName, '/') . '/' . $url;
    }

    /**
     * Resolves a relative URL using directories, for instance, if the
     * current directory is "path/to/something", and you want to get the
     * relative URL to "path/to/something/else.html", the result will
     * be else.html. Else, "../" will be added to go to the upper directory
     */
    public function relativeUrl(?string $url, string $currentFileName) : ?string
    {
        if ($url === null) {
            return null;
        }

        // If string contains ://, it is considered as absolute
        if (preg_match('/:\\/\\//mUsi', $url) > 0) {
            return $url;
        }

        // If string begins with "/", the "/" is removed to resolve the
        // relative path
        if ($url !== '' && $url[0] === '/') {
            $url = substr($url, 1);

            if ($this->samePrefix($url, $currentFileName)) {
                // If the prefix is the same, simply returns the file name
                $relative = basename($url);
            } else {
                // Else, returns enough ../ to get upper
                $relative = '';

                $depth = count(explode('/', $currentFileName)) - 1;

                for ($k = 0; $k < $depth; $k++) {
                    $relative .= '../';
                }

                $relative .= $url;
            }
        } else {
            $relative = $url;
        }

        return $relative;
    }

    public function canonicalUrl(string $dirName, string $url) : ?string
    {
        if ($url !== '') {
            if ($url[0] === '/') {
                // If the URL begins with a "/", the following is the
                // canonical URL
                return substr($url, 1);
            }

            // Else, the canonical name is under the current dir
            if ($dirName !== '') {
                $path = $url;

                // the url is already a canonical url
                if (strpos($url, $dirName . '/') === 0) {
                    return $url;
                }

                return $this->canonicalize($dirName . '/' . $path);
            }

            return $this->canonicalize($url);
        }

        return null;
    }

    private function canonicalize(string $url) : string
    {
        $parts = explode('/', $url);
        $stack = [];

        foreach ($parts as $part) {
            if ($part === '..') {
                array_pop($stack);
            } else {
                $stack[] = $part;
            }
        }

        return implode('/', $stack);
    }

    private function samePrefix(string $url, string $currentFileName) : bool
    {
        $partsA = explode('/', $url);
        $partsB = explode('/', $currentFileName);

        $n = count($partsA);

        if ($n !== count($partsB)) {
            return false;
        }

        unset($partsA[$n - 1]);
        unset($partsB[$n - 1]);

        return $partsA === $partsB;
    }
}
