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

    /** @var array  */
    private $locationParts = [];

    /** @var array  */
    private $dsnParts = [];

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
        $this->dsnParts = explode(';', $dsn);
        $location = $this->dsnParts[0];
        unset($this->dsnParts[0]);
        $this->locationParts = parse_url($location);

        if (! $this->locationParts) {
            throw new \InvalidArgumentException(
                sprintf('"%s" is not a valid DSN.', $dsn)
            );
        }


        $this->parseScheme();

        $this->parseHostAndPath();

        $this->parsePort();

        $this->parseUser();

        $this->parsePassword();

        $this->parseQuery();

        $this->parseParameters();
    }

    /**
     * validates and sets the scheme property
     */
    private function parseScheme()
    {
        if (array_key_exists('scheme', $this->locationParts)) {
            $validSchemes = ['file', 'git+http', 'git+https'];
            if (! in_array(strtolower($this->locationParts['scheme']), $validSchemes)) {
                throw new \InvalidArgumentException(
                    sprintf('"%s" is not a valid scheme.', $this->locationParts['scheme'])
                );
            }
            $this->scheme = $this->locationParts['scheme'];

        } else {

            $this->scheme = 'file';
        }
    }

    /**
     * Validates and sets the host and path properties
     */
    private function parseHostAndPath()
    {
        if (array_key_exists('scheme', $this->locationParts) && $this->locationParts['scheme'] === 'file') {
            $path = array_key_exists('path', $this->locationParts) ? $this->locationParts['path'] : "";
            $path = $this->locationParts['host'] . $path;
            $result = preg_match("/^([a-zA-Z0-9-_.~\/]*(%([A-F]|[0-9]){2})*)*$/", $path);
            if ($result === 0) {
                throw new \InvalidArgumentException(
                    sprintf('"%s" is not a valid path.', $this->locationParts['path'])
                );
            }

            $this->path = $path;

        } else {
            if (array_key_exists('host', $this->locationParts)) {
                $result = preg_match("/^([a-zA-Z0-9-_.~]*(%([A-F]|[0-9]){2})*)*$/", $this->locationParts['host']);
                if ($result === 0) {
                    throw new \InvalidArgumentException(
                        sprintf('"%s" is not a valid host.', $this->locationParts['host'])
                    );
                }
                $this->host = $this->locationParts['host'];
            }

            if (array_key_exists('path', $this->locationParts)) {
                $result = preg_match("/^([a-zA-Z0-9-_.~\/]*(%([A-F]|[0-9]){2})*)*$/", $this->locationParts['path']);
                if ($result === 0) {
                    throw new \InvalidArgumentException(
                        sprintf('"%s" is not a valid path.', $this->locationParts['path'])
                    );
                }
                $this->path = $this->locationParts['path'];
            }
        }
    }

    /**
     * validates and sets the port property
     */
    private function parsePort()
    {
        if (array_key_exists('port', $this->locationParts)) {
            if (!is_int($this->locationParts['port'])) {
                throw new \InvalidArgumentException(
                    sprintf('"%s" is not a valid port.', $this->locationParts['port'])
                );
            }
            $this->port = $this->locationParts['port'];
        }
    }

    /**
     * validates and sets the user property
     */
    private function parseUser()
    {
        if (array_key_exists('user', $this->locationParts)) {
            $result = preg_match("/^([a-zA-Z0-9-_.~!$&'()+*,=]*(%([A-F]|[0-9]){2})*)*$/", $this->locationParts['user']);

            if ($result === 0) {
                throw new \InvalidArgumentException(
                    sprintf('"%s" is not a valid username.', $this->locationParts['user'])
                );
            }
            $this->user = $this->locationParts['user'];
        }

    }
    /**
     * validates and sets the password property
     */
    private function parsePassword()
    {
        if (array_key_exists('pass', $this->locationParts)) {
            $result = preg_match(
                "/^([a-zA-Z0-9-_.~!$&'()+*,=:]*(%([A-F]|[0-9]){2})*)*$/",
                $this->locationParts['pass']
            );

            if ($result === 0) {
                throw new \InvalidArgumentException(
                    sprintf('"%s" is not a valid password.', $this->locationParts['pass'])
                );
            }
            $this->password = $this->locationParts['pass'];
        }

    }

    /**
     * validates and sets the query property
     */
    private function parseQuery()
    {
        if (array_key_exists('query', $this->locationParts)) {
            $queryParts = explode('?', $this->locationParts['query']);
            foreach ($queryParts as $part) {
                $result = preg_match("/^([a-zA-Z0-9-_.~!$&'()+*,=:\/@]*(%([A-F]|[0-9]){2})*)*$/", $part);

                if ($result === 0) {
                    throw new \InvalidArgumentException(
                        sprintf('"%s" is not a valid query.', $part)
                    );
                }
            }
            $this->query = $queryParts;
        }
    }

    /**
     * validates and sets the parameters property
     */
    private function parseParameters()
    {
        if (!empty($this->dsnParts)) {
            foreach ($this->dsnParts as $part) {
                $result = preg_match("/^[a-zA-Z0-9-_]+=([a-zA-Z0-9-_.~!$&'()+*,=:\/@]*(%([A-F]|[0-9]){2})*)*$/", $part);

                if ($result === 0) {
                    throw new \InvalidArgumentException(
                        sprintf('"%s" is not a valid parameter.', $part)
                    );
                }

                $option = explode('=', $part);
                $this->parameters[$option[0]] = $option[1];
            }
        }
    }
}
