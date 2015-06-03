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

    /** @var array  */
    private $query = [];

    /** @var array  */
    private $parameters = [];

    /**
     * @param $dsn
     */
    public function __construct($dsn)
    {
        $this->parse($dsn);
    }

    /**
     * @return string
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return new Path($this->path);
    }

    /**
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @return string
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param $dsn
     */
    private function parse($dsn)
    {
        $dsnParts = explode(';', $dsn);
        $location = $dsnParts[0];
        unset($dsnParts[0]);
        $locationParts = parse_url($location);

        $options = array();
        foreach ($dsnParts as $part) {
            $option = explode('=', $part);
            $options[$option[0]] = $option[1];
        }

        $this->scheme = array_key_exists('scheme', $locationParts) ? $locationParts['scheme'] : "";

        if (array_key_exists('scheme', $locationParts) && $locationParts['scheme'] === 'file') {

            $path = array_key_exists('path', $locationParts) ? $locationParts['path'] : "";
            $this->path = $locationParts['host'] . $path;
        } else {
            $this->host = array_key_exists('host', $locationParts) ? $locationParts['host'] : "";
            $this->path = array_key_exists('path', $locationParts) ? $locationParts['path'] : "";
        }

        $this->port = array_key_exists('port', $locationParts) ? $locationParts['port'] : "";

        $this->user = array_key_exists('user', $locationParts) ? $locationParts['user'] : "";

        $this->password = array_key_exists('pass', $locationParts) ? $locationParts['pass'] : "";

        $query = array_key_exists('query', $locationParts) ? $locationParts['query'] : "";
        $this->query = !empty($query) ? explode('?', $query) : [];

        $this->parameters = !empty($options) ? $options : [];
    }
}
