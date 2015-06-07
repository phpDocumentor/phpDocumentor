<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2015 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor;

/**
 * Value Object for DSN.
 */
final class Dsn
{
    /** @var string */
    private $scheme;

    /** @var string */
    private $host;

    /** @var int */
    private $port;

    /** @var string */
    private $user;

    /** @var string */
    private $password;

    /** @var string */
    private $path;

    /** @var string[]  */
    private $query = [];

    /** @var string[]  */
    private $parameters = [];

    /**
     * Initializes the Dsn
     *
     * @param string $dsn
     */
    public function __construct($dsn)
    {
        $this->parse($dsn);
    }

    /**
     * Returns the scheme part of the DSN
     *
     * @return string
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * Returns the host part of the DSN
     *
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Returns the port part of the DSN
     *
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Returns the username part of the DSN
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->user;
    }

    /**
     * Returns the password part of the DSN
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Returns the path part of the DSN
     *
     * @return string
     */
    public function getPath()
    {
        return new Path($this->path);
    }

    /**
     * Returns the query part of the DSN
     *
     * @return string[]
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Returns the parameters part of the DSN
     *
     * @return string[]
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Parses the given DSN
     *
     * @param string $dsn
     * @return void
     */
    private function parse($dsn)
    {
        if (! is_string($dsn)) {
            throw new \InvalidArgumentException(
                sprintf('"%s" is not a valid DSN.', var_export($dsn, true))
            );
        }

        $dsnParts = explode(';', $dsn);
        $location = $dsnParts[0];
        unset($dsnParts[0]);
        $locationParts = parse_url($location);

        if (! isset($locationParts['scheme'])) {
            $locationParts['scheme'] = 'file';
            $location = 'file://' . $location;
        }

        if (! filter_var($location, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException(
                sprintf('"%s" is not a valid DSN.', $dsn)
            );
        }

        $this->parseScheme($locationParts);

        $this->parseHostAndPath($locationParts);

        $this->parsePort($locationParts);

        $this->user = isset($locationParts['user']) ? $locationParts['user'] : "";

        $this->password = isset($locationParts['pass']) ? $locationParts['pass'] : "";

        $this->parseQuery($locationParts);

        $this->parseParameters($dsnParts);
    }

    /**
     * validates and sets the scheme property
     *
     * @param string $locationParts
     * @return void
     */
    private function parseScheme($locationParts)
    {
        $validSchemes = ['file', 'git+http', 'git+https'];
        if (! in_array(strtolower($locationParts['scheme']), $validSchemes)) {
            throw new \InvalidArgumentException(
                sprintf('"%s" is not a valid scheme.', $locationParts['scheme'])
            );
        }
        $this->scheme = strtolower($locationParts['scheme']);
    }

    /**
     * Validates and sets the host and path properties
     *
     * @param string $locationParts
     * @return void
     */
    private function parseHostAndPath($locationParts)
    {
        $path = isset($locationParts['path']) ? $locationParts['path'] : "";
        $host = isset($locationParts['host']) ? $locationParts['host'] : "";

        if ($this->getScheme() === 'file') {
            $this->path = $host . $path;
        } else {
            $this->host = $host;
            $this->path = $path;
        }
    }

    /**
     * Validates and sets the port property
     *
     * @param string $locationParts
     * @return void
     */
    private function parsePort($locationParts)
    {
        if (! isset($locationParts['port'])) {
            if ($this->getScheme() === 'git+http') {
                $this->port = 80;
            } elseif ($this->getScheme() === 'git+https') {
                $this->port = 443;
            } else {
                $this->port = 0;
            }
        } else {
            $this->port = $locationParts['port'];
        }
    }

    /**
     * validates and sets the query property
     *
     * @param string $locationParts
     * @return void
     */
    private function parseQuery($locationParts)
    {
        if (isset($locationParts['query'])) {
            $queryParts = explode('&', $locationParts['query']);
            foreach ($queryParts as $part) {
                $option = $this->splitKeyValuePair($part);

                $this->query[$option[0]] = $option[1];
            }
        }
    }

    /**
     * validates and sets the parameters property
     *
     * @param string $dsnParts
     * @return void
     */
    private function parseParameters($dsnParts)
    {
        if (!empty($dsnParts)) {
            foreach ($dsnParts as $part) {
                $option = $this->splitKeyValuePair($part);

                $this->parameters[$option[0]] = $option[1];
            }
        }
    }

    /**
     * Splits a key-value pair
     *
     * @param string $pair
     * @return string[] $option
     */
    private function splitKeyValuePair($pair)
    {
        $option = explode('=', $pair);
        if (count($option) != 2) {
            throw new \InvalidArgumentException(
                sprintf('"%s" is not a valid query or parameter.', $pair)
            );
        }

        return $option;
    }
}
