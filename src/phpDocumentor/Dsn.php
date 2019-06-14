<?php
declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor;

use InvalidArgumentException;

/**
 * Value Object for DSN.
 */
final class Dsn
{
    /** @var string */
    private $dsn;

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

    /** @var string[] */
    private $query = [];

    /** @var string[] */
    private $parameters = [];

    //@codingStandardsIgnoreStart
    const WINDOWS_DSN = '~(^((?<scheme>file):\\/\\/)?(?<path>((?:[a-z]|[A-Z]):(?=\\\\(?![\\0-\\37<>:"/\\\\|?*])|\\/(?![\\0-\\37<>:"/\\\\|?*])|$)|^\\\\(?=[\\\\\\/][^\\0-\\37<>:"/\\\\|?*]+)|^(?=(\\\\|\\/)$)|^\\.(?=(\\\\|\\/)$)|^\\.\\.(?=(\\\\|\\/)$)|^(?=(\\\\|\\/)[^\\0-\\37<>:"/\\\\|?*]+)|^\\.(?=(\\\\|\\/)[^\\0-\\37<>:"/\\\\|?*]+)|^\\.\\.(?=(\\\\|\\/)[^\\0-\\37<>:"/\\\\|?*]+))((\\\\|\\/)[^\\0-\\37<>:"/\\\\|?*]+|(\\\\|\\/)$)*()))$~';

    //@codingStandardsIgnoreEnd

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
    public function __toString(): string
    {
        return $this->dsn;
    }

    /**
     * Returns the scheme part of the DSN
     */
    public function getScheme(): string
    {
        return $this->scheme;
    }

    /**
     * Returns the host part of the DSN
     */
    public function getHost(): ?string
    {
        return $this->host;
    }

    /**
     * Returns the port part of the DSN
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * Returns the username part of the DSN
     */
    public function getUsername(): string
    {
        return $this->user;
    }

    /**
     * Returns the password part of the DSN
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * Returns the path part of the DSN
     */
    public function getPath(): Path
    {
        return new Path($this->path);
    }

    /**
     * Returns the query part of the DSN
     *
     * @return string[]
     */
    public function getQuery(): array
    {
        return $this->query;
    }

    /**
     * Returns the parameters part of the DSN
     *
     * @return string[]
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * Parses the given DSN
     */
    private function parse(string $dsn): void
    {
        $dsnParts = explode(';', $dsn);
        $location = $dsnParts[0];
        unset($dsnParts[0]);
        $locationParts = parse_url($location);

        if ($locationParts === false ||
            (array_key_exists('scheme', $locationParts) && \strlen($locationParts['scheme']) === 1)
        ) {
            preg_match(static::WINDOWS_DSN, $dsn, $locationParts);
        }

        if (! array_key_exists('scheme', $locationParts) ||
            ($locationParts['scheme'] === '' && array_key_exists('path', $locationParts))
        ) {
            $locationParts['scheme'] = 'file';
            $location = 'file://' . $location;
        }

        if (!filter_var($location, FILTER_VALIDATE_URL) && !preg_match(static::WINDOWS_DSN, $location)) {
            throw new InvalidArgumentException(
                sprintf('"%s" is not a valid DSN.', $dsn)
            );
        }

        $this->parseDsn($location, $dsnParts);

        $this->parseScheme($locationParts);

        $this->parseHostAndPath($locationParts);

        $this->parsePort($locationParts);

        $this->user = $locationParts['user'] ?? '';

        $this->password = $locationParts['pass'] ?? '';

        $this->parseQuery($locationParts);

        $this->parseParameters($dsnParts);
    }

    /**
     * Reconstructs the original DSN but
     * when scheme was omitted in the original DSN, it will now be file://
     *
     * @param string[] $dsnParts
     */
    private function parseDsn(string $location, array $dsnParts): void
    {
        array_splice($dsnParts, 0, 0, $location);
        $this->dsn = implode(';', $dsnParts);
    }

    /**
     * validates and sets the scheme property
     *
     * @param string[] $locationParts
     * @throws InvalidArgumentException
     */
    private function parseScheme(array $locationParts): void
    {
        if (! $this->isValidScheme($locationParts['scheme'])) {
            throw new InvalidArgumentException(
                sprintf('"%s" is not a valid scheme.', $locationParts['scheme'])
            );
        }

        $this->scheme = strtolower($locationParts['scheme']);
    }

    /**
     * Validated provided scheme.
     */
    private function isValidScheme(string $scheme): bool
    {
        $validSchemes = ['file', 'git+http', 'git+https'];
        return \in_array(\strtolower($scheme), $validSchemes, true);
    }

    /**
     * Validates and sets the host and path properties
     *
     * @param string[] $locationParts
     */
    private function parseHostAndPath(array $locationParts): void
    {
        $path = $locationParts['path'] ?? '';
        $host = $locationParts['host'] ?? '';

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
     * @param string[] $locationParts
     */
    private function parsePort(array $locationParts): void
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
            $this->port = (int) $locationParts['port'];
        }
    }

    /**
     * validates and sets the query property
     *
     * @param string[] $locationParts
     */
    private function parseQuery(array $locationParts): void
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
     * @param string[] $dsnParts
     */
    private function parseParameters(array $dsnParts): void
    {
        foreach ($dsnParts as $part) {
            $option = $this->splitKeyValuePair($part);

            $this->parameters[$option[0]] = $option[1];
        }
    }

    /**
     * Splits a key-value pair
     *
     * @return string[]
     * @throws InvalidArgumentException
     */
    private function splitKeyValuePair(string $pair): array
    {
        $option = explode('=', $pair);
        if (count($option) !== 2) {
            throw new InvalidArgumentException(
                sprintf('"%s" is not a valid query or parameter.', $pair)
            );
        }

        return $option;
    }
}
