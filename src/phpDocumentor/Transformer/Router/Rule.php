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

namespace phpDocumentor\Transformer\Router;

/**
 * A rule determines if and how a node should be transformed to an URL.
 */

use phpDocumentor\Descriptor\DescriptorAbstract;
use function explode;
use function extension_loaded;
use function iconv;
use function implode;
use function substr;
use function urlencode;

class Rule
{
    /** @var callable */
    protected $generator;

    /** @var callable */
    protected $matcher;

    /**
     * Initializes this rule.
     *
     * @param callable $matcher   A closure that returns a boolean indicating whether this rule applies to the
     *     provided node.
     * @param callable $generator A closure that returns a url or null for the given node.
     */
    public function __construct(callable $matcher, callable $generator)
    {
        $this->matcher   = $matcher;
        $this->generator = $generator;
    }

    /**
     * Returns true when this rule is applicable to the given node.
     *
     * @param string|DescriptorAbstract $node
     */
    public function match($node) : bool
    {
        $callable = $this->matcher;

        return $callable($node);
    }

    /**
     * Generates an URL for the given node.
     *
     * @param string|DescriptorAbstract $node The node for which to generate an URL.
     *
     * @return string|false a well-formed relative or absolute URL, or false if no URL could be generated.
     */
    public function generate($node)
    {
        $callable            = $this->generator;
        $generatedPathAsUtf8 = $callable($node);

        return $generatedPathAsUtf8 ? $this->translateToUrlEncodedPath($generatedPathAsUtf8) : false;
    }

    /**
     * Translates the provided path, with UTF-8 characters, into a web- and windows-safe variant.
     *
     * Windows does not support the use of UTF-8 characters on their file-system. In order to be sure that both
     * the web and windows can support the given filename we decode the UTF-8 characters and then url encode them
     * so that they will be plain characters that are suitable for the web.
     *
     * If an anchor is found in the path, then it is neither url_encoded not transliterated because it should not
     * result in a filename (otherwise another part of the application has made an error) but may be used during
     * rendering of templates.
     */
    protected function translateToUrlEncodedPath(string $generatedPathAsUtf8) : string
    {
        $iso88591Path = explode('/', $generatedPathAsUtf8);

        foreach ($iso88591Path as &$part) {
            // identify and skip schemes
            if (substr($part, -1) === ':') {
                continue;
            }

            // only encode and transliterate that which comes before the anchor
            $subparts = explode('#', $part);

            if (extension_loaded('iconv')) {
                $subparts[0] = iconv('UTF-8', 'ASCII//TRANSLIT', $subparts[0]);
            }

            $subparts[0] = urlencode($subparts[0]);

            $part = implode('#', $subparts);
        }

        return implode('/', $iso88591Path);
    }
}
