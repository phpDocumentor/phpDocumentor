<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Parser\Loader;

final class Uri
{
    /** @var string The protocol via which the files bound to this location should be retrieved */
    private $scheme = 'file';

    /** @var string the username with which to authenticate against the service in the protocol */
    private $username = '';

    /** @var string the password with which to authenticate against the service in the protocol */
    private $password = '';

    /** @var string The host from which remote files should be retrieved */
    private $host = '';

    /** @var string The path on the host where the files should be retrieved */
    private $path = '';

    /** @var string options added to the uri */
    private $options = array();

    /** @var string[] list of query string parameters appended to the URI */
    private $query;

    /** @var string the fragment following the # in a URI */
    private $fragment;

    /** @var string the original URI string */
    private $original = '';

    /**
     * Initializes this value object with the given URI.
     *
     * @param string $uri
     *
     * @throws \InvalidArgumentException if the provided uri is not a string.
     */
    public function __construct($uri)
    {
        if (! is_string($uri)) {
            throw new \InvalidArgumentException(
                'The URI is expected to be a string, but "' . gettype($uri) . '" was found'
            );
        }

        $this->parse($uri);
    }

    /**
     * Returns the username.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Returns the password.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Returns the scheme name for this URI without the `://`.
     *
     * The list of schemes matches the list provided by
     * [IANA](http://www.iana.org/assignments/uri-schemes/uri-schemes.xhtml) as much as possible. Sometimes a combined
     * scheme is necessary in order to correctly recognize a specific location.
     *
     * An example of combined scheme may be `git+http`; where the communication protocol is git but the carrier protocol
     * is http. A full example could be: `git+http://github.com/phpDocumentor/phpDocumentor2`; without the preceding
     * git we would not be able to determine that this URI should be approached using git but over http.
     *
     * @return string
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * Returns the host name to which this URI refers if it points to a remote resource..
     *
     * Do note that the host does not have a scheme attached to it.
     *
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Returns the path on the host where the resource is located.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Returns the options on this URI.
     *
     * In the official RFC a URI can have options on each path segment using semi-colons. For the purposes of the Loader
     * we are going to assume that the options on the last path segment belong to the entire URI. This method returns
     * that as a key => value pair.
     *
     * For example:
     *
     *     http://github.com/phpDocumentor/phpDocumentor;branch=master
     *
     * has a single option called `branch` with the value `master`. Multiple options may be provided that are
     * separated by a semicolon.
     *
     * It is important to remember that the options are part of the path and should precede the query string, so this is
     * *good*:
     *
     *      http://github.com/phpDocumentor/phpDocumentor;branch=master?queryParameter=true
     *
     * But if you put the options after the query string than it won't be recognized but instead treated as part of the
     * query string; this is an example of what does *not* work:
     *
     *
     *      http://github.com/phpDocumentor/phpDocumentor?queryParameter=true;branch=master
     *
     * @return string[]
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Returns the option with the given name or null if that does not exist.
     *
     * @param string $name
     *
     * @see self::getOptions() for more information on how options work.
     *
     * @return string
     */
    public function getOption($name)
    {
        return isset($this->options[$name]) ? $this->options[$name] : null;
    }

    /**
     * Returns the original URI.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->original;
    }

    /**
     * Interprets the given URI and populates the properties in this value object.
     *
     * @param string $uri
     *
     * @return void
     */
    private function parse($uri)
    {
        $uri = $this->normalizeGithubAddressWithoutScheme($uri);

        $this->original = $uri;
        $parts = parse_url($uri);

        list($options, $this->path) = $this->splitPathIntoOptionsAndPath(isset($parts['path']) ? $parts['path'] : '');
        $this->scheme = isset($parts['scheme']) ? $parts['scheme'] : $this->scheme;
        $this->host = isset($parts['host']) ? $parts['host'] : $this->host;
        $this->username = isset($parts['user']) ? $parts['user'] : $this->username;
        $this->password = isset($parts['pass']) ? $parts['pass'] : $this->password;
        $this->fragment = isset($parts['fragment']) ? $parts['fragment'] : $this->fragment;
        if (isset($parts['query'])) {
            parse_str($parts['query'], $this->query);
        }

        foreach ($options as $option) {
            list($key, $value) = $this->convertOptionIntoKeyValuePair($option);
            if ($key === null) {
                continue;
            }

            $this->options[$key] = $value;
        }
    }

    private function normalizeGithubAddressWithoutScheme($locationString)
    {
        $test = 'github.com/';
        if (substr($locationString, 0, strlen($test) == $test)) {
            return 'git+https://' . $locationString;
        }

        return $locationString;
    }

    /**
     * @param $path
     * @return array
     */
    private function splitPathIntoOptionsAndPath($path)
    {
        $options = explode(';', $path);
        $path = array_shift($options) ?: '';

        return array($options, $path);
    }

    /**
     * @param $option
     * @return array
     */
    private function convertOptionIntoKeyValuePair($option)
    {
        if (!$option) {
            return array(null, null);
        }

        $option = explode('=', $option);
        $key    = trim($option[0]);
        $value  = isset($option[1]) ? trim($option[1]) : true;

        return array($key, $value);
    }
}
