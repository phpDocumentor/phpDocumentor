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

namespace phpDocumentor;

use Generator;
use League\Uri\Contracts\UriInterface;
use League\Uri\UriInfo;
use League\Uri\UriResolver;

use function array_shift;
use function array_splice;
use function explode;
use function implode;
use function ltrim;
use function parse_str;
use function preg_match;
use function rtrim;
use function strpos;

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

    /** @var UriInterface */
    private $uri;

    /** @var string[] */
    private $parameters;

    /**
     * Initializes the Dsn
     *
     * @param array<string> $parameters
     */
    public function __construct(UriInterface $uri, array $parameters, string $dsn)
    {
        $this->dsn = $dsn;
        $this->parameters = $parameters;
        $this->uri = $uri;
    }

    public static function createFromString(string $dsn): self
    {
        $parameters       = explode(';', $dsn);
        $uri              = UriFactory::createUri(array_shift($parameters));
        $parsedParameters = self::parseParameters($parameters);

        array_splice($parameters, 0, 0, (string) $uri);
        $dsn = implode(';', $parameters);

        return new self($uri, $parsedParameters, $dsn);
    }

    /**
     * @param array<string> $parameters
     */
    public static function createFromUri(UriInterface $uri, array $parameters = []): self
    {
        $dsn = implode(';', [(string) $uri] + $parameters);

        return new self($uri, $parameters, $dsn);
    }

    /**
     * Returns a string representation of the DSN.
     */
    public function __toString(): string
    {
        if ($this->getScheme() === 'phar' && $this->isWindowsLocalPath()) {
            return 'phar://' . $this->getPath();
        }

        return $this->dsn;
    }

    /**
     * Returns the scheme part of the DSN
     */
    public function getScheme(): ?string
    {
        return $this->uri->getScheme();
    }

    /**
     * Returns the host part of the DSN
     */
    public function getHost(): string
    {
        return $this->uri->getHost() ?? '';
    }

    /**
     * Returns the port part of the DSN
     */
    public function getPort(): ?int
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
    public function getUsername(): string
    {
        return explode(':', $this->uri->getUserInfo() ?? '')[0];
    }

    /**
     * Returns the password part of the DSN
     */
    public function getPassword(): string
    {
        return explode(':', $this->uri->getUserInfo() ?? '')[1] ?? '';
    }

    /**
     * Returns the path part of the DSN
     */
    public function getPath(): Path
    {
        if ($this->isWindowsLocalPath()) {
            return new Path(ltrim($this->uri->getPath(), '/'));
        }

        return new Path($this->uri->getPath() ?: '/');
    }

    public function isWindowsLocalPath(): bool
    {
        $path = ltrim($this->uri->getPath(), '/');

        return preg_match(UriFactory::WINDOWS_URI_FORMAT, $path) === 1;
    }

    /**
     * Returns the query part of the DSN
     *
     * @return string[]
     */
    public function getQuery(): array
    {
        $result = [];
        parse_str($this->uri->getQuery() ?? '', $result);

        return $result;
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

    public function resolve(Dsn $baseDsn): self
    {
        if (UriInfo::isAbsolute($this->uri) || UriInfo::isAbsolutePath($this->uri)) {
            return $this;
        }

        $baseUri = rtrim(((string) $baseDsn->uri), '/');
        $newUri  = UriFactory::createUri($baseUri . '/' . $this->uri->getPath());

        return self::createFromUri(
            UriResolver::resolve($newUri, $baseDsn->uri),
            $baseDsn->parameters
        );
    }

    public function withPath(Path $path): self
    {
        $pathString = (string) $path;
        if (strpos($pathString, '/') !== 0) {
            $pathString = '/' . $pathString;
        }

        return self::createFromUri($this->uri->withPath($pathString), $this->parameters);
    }

    /**
     * validates and sets the parameters property
     *
     * @param string[] $parameters
     *
     * @return array<string, string>
     */
    private static function parseParameters(array $parameters): array
    {
        $result = [];
        foreach ($parameters as $parameter) {
            foreach (self::parseParameter($parameter) as $key => $value) {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * @return Generator<string, string>
     */
    private static function parseParameter(string $part): Generator
    {
        $result = [];
        parse_str($part, $result);

        yield from $result;
    }
}
