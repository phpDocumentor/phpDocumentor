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

namespace phpDocumentor;

use InvalidArgumentException;
use League\Uri\Uri as LeagueUri;
use Throwable;
use function array_shift;
use function array_splice;
use function explode;
use function implode;
use function parse_str;
use function preg_match;
use function sprintf;

/**
 * Data Source Name (DSN), a reference to a path on a local or remote system with the ability to add parameters.
 *
 * The format for the DSN is inspired by the PDO DSN format
 * ({@see https://www.php.net/manual/en/ref.pdo-mysql.connection.php}) where you have a string containing Semicolon
 * Separated Values (SSV), where each part is a key=value pair. Exception to this rule is the first entry in the string;
 * this is the URI where the referenced Data Source is located.
 *
 * A simple example can be a reference to your project folder:
 *
 *     file:///home/mvriel/project/src
 *
 * Yet a more complex example may be a reference to a specific branch on a git repository:
 *
 *     git+https://github.com/phpDocumentor/phpDocumentor;path=/src;branch=release/3.0
 *
 * In the example above we reference a git repository using the http protocol and as options we mention that the branch
 * that we would like to parse is `release/3.0` and in it we want to start at the path `/src`.
 */
final class Dsn
{
    /** @var string */
    private $dsn;

    /** @var LeagueUri */
    private $uri;

    /** @var string[] */
    private $parameters = [];

    /**
     * Initializes the Dsn
     */
    public function __construct(string $dsn)
    {
        $this->parse($dsn);
    }

    /**
     * Returns a string representation of the DSN.
     */
    public function __toString() : string
    {
        return $this->dsn;
    }

    /**
     * Returns the scheme part of the DSN
     */
    public function getScheme() : ?string
    {
        return $this->uri->getScheme();
    }

    /**
     * Returns the host part of the DSN
     */
    public function getHost() : string
    {
        return $this->uri->getHost() ?: '';
    }

    /**
     * Returns the port part of the DSN
     */
    public function getPort() : ?int
    {
        $port = $this->uri->getPort();
        if ($port !== null) {
            return $port;
        }

        switch ($this->uri->getScheme()) {
            case 'http':
            case 'git+http':
                return 80;
            case 'https':
            case 'git+https':
                return 443;
            default:
                return null;
        }
    }

    /**
     * Returns the username part of the DSN
     */
    public function getUsername() : string
    {
        return explode(':', $this->uri->getUserInfo() ?: '')[0];
    }

    /**
     * Returns the password part of the DSN
     */
    public function getPassword() : string
    {
        return explode(':', $this->uri->getUserInfo() ?: '')[1] ?? '';
    }

    /**
     * Returns the path part of the DSN
     */
    public function getPath() : Path
    {
        return new Path($this->uri->getPath() ?: '/');
    }

    /**
     * Returns the query part of the DSN
     *
     * @return string[]
     */
    public function getQuery() : array
    {
        $result = [];
        parse_str($this->uri->getQuery() ?: '', $result);

        return $result;
    }

    /**
     * Returns the parameters part of the DSN
     *
     * @return string[]
     */
    public function getParameters() : array
    {
        return $this->parameters;
    }

    /**
     * Parses the given DSN
     */
    private function parse(string $dsn) : void
    {
        $parameters = explode(';', $dsn);
        $this->uri = $this->parseUri(array_shift($parameters));
        $this->parseParameters($parameters);

        array_splice($parameters, 0, 0, (string) $this->uri);
        $this->dsn = implode(';', $parameters);
    }

    /**
     * validates and sets the parameters property
     *
     * @param string[] $parameters
     */
    private function parseParameters(array $parameters) : void
    {
        foreach ($parameters as $parameter) {
            $this->parseParameter($parameter);
        }
    }

    private function parseParameter(string $part) : void
    {
        $result = [];
        parse_str($part, $result);

        foreach ($result as $key => $value) {
            $this->parameters[$key] = $value;
        }
    }

    private function parseUri(string $uriString) : LeagueUri
    {
        try {
            if (preg_match('~^[a-zA-Z]+:\\\\~', $uriString)) {
                return LeagueUri::createFromWindowsPath($uriString);
            }

            return LeagueUri::createFromString($uriString);
        } catch (Throwable $exception) {
            throw new InvalidArgumentException(
                sprintf(
                    'The DSN "%s" could not be parsed, the following error occured: %s',
                    $uriString,
                    $exception->getMessage()
                ),
                0,
                $exception
            );
        }
    }
}
