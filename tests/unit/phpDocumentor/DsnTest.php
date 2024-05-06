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

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \phpDocumentor\Dsn
 * @covers ::__construct
 
 */
class DsnTest extends TestCase
{
    /**
     * @uses         \phpDocumentor\Path
     *
     * @covers ::getScheme
     * @covers ::getUsername
     * @covers ::getPassword
     * @covers ::getPort
     * @covers ::getHost
     * @covers ::getPath
     * @covers ::getQuery
     * @covers ::getParameters
     * @covers ::__toString
     * @covers ::createFromString
     *
     * @dataProvider provideDsnsToTestAgainst
     */
    public function testValidDsnWithScheme(
        string $dsn,
        string $normalizedDsn,
        string|null $scheme,
        string $path = '',
        string $host = '',
        int|null $port = null,
        string $user = '',
        string $pass = '',
        array $query = [],
        array $parameters = [],
    ): void {
        $fixture = Dsn::createFromString($dsn);

        $this->assertSame($normalizedDsn, (string) $fixture, 'Conversion to string fails');
        $this->assertSame($scheme, $fixture->getScheme(), 'Scheme does not match');
        $this->assertSame($user, $fixture->getUsername(), 'Username does not match');
        $this->assertSame($pass, $fixture->getPassword(), 'Password does not match');
        $this->assertSame($port, $fixture->getPort(), 'Port does not match');
        $this->assertSame($host, $fixture->getHost(), 'Host does not match');
        $this->assertEquals(new Path($path), $fixture->getPath(), 'Path does not match');
        $this->assertSame($query, $fixture->getQuery(), 'Query does not match');
        $this->assertSame($parameters, $fixture->getParameters(), 'Parameters do not match');
    }

    /**
     * @covers ::createFromString
     * @covers ::withPath
     * @covers ::getPath
     */
    public function testPathNotStartingWithSlash(): void
    {
        $dns = Dsn::createFromString('file://test');
        $dns = $dns->withPath(new Path('PathWithoutSlash'));
        $this->assertEquals(new Path('/PathWithoutSlash'), $dns->getPath(), 'Path does not match');
    }

    public static function provideDsnsToTestAgainst(): array
    {
        return [
            'test the most elaborate example of a DSN' => [
                'git+http://user:pw@github.com:8000/phpDocumentor/phpDocumentor2?q=qry1&x=qry2;branch=dev;other=xxx',
                'git+http://user:pw@github.com:8000/phpDocumentor/phpDocumentor2?q=qry1&x=qry2;branch=dev;other=xxx',
                'git+http',
                '/phpDocumentor/phpDocumentor2',
                'github.com',
                8000,
                'user',
                'pw',
                ['q' => 'qry1', 'x' => 'qry2'],
                ['branch' => 'dev', 'other' => 'xxx'],
            ],
            'test that a URI pointing to a file in a phar works' => [
                'phar:///app/build/phpDocumentor.phar/src/phpDocumentor/../../data/templates',
                'phar:///app/build/phpDocumentor.phar/src/phpDocumentor/../../data/templates',
                'phar',
                '/app/build/phpDocumentor.phar/src/phpDocumentor/../../data/templates',
            ],
            'test that a URI pointing to a file in a phar works on windows' => [
                'phar://c:/app/build/phpDocumentor.phar/src/phpDocumentor/../../data/templates',
                'phar://c:/app/build/phpDocumentor.phar/src/phpDocumentor/../../data/templates',
                'phar',
                'c:/app/build/phpDocumentor.phar/src/phpDocumentor/../../data/templates',
            ],
            'test local file on windows without scheme' => [
                'C:\\phpdocumentor\\tests\\unit\\phpDocumentor\\Parser',
                'file:///C:/phpdocumentor/tests/unit/phpDocumentor/Parser',
                'file',
                'C:/phpdocumentor/tests/unit/phpDocumentor/Parser',
            ],
            'test local file on windows without scheme and forward slashes' => [
                'c:/phpdocumentor/tests/unit/phpDocumentor/Parser',
                'file:///c:/phpdocumentor/tests/unit/phpDocumentor/Parser',
                'file',
                'c:/phpdocumentor/tests/unit/phpDocumentor/Parser',
            ],
            'test relative local file on unix without scheme' => [
                'project/src',
                'project/src',
                // This is a relative URI reference and does not have a scheme,
                // see https://tools.ietf.org/html/rfc3986#section-4.1
                null,
                'project/src',
            ],
            'test absolute local file on unix without scheme' => [
                '/opt/data/project/src',
                '/opt/data/project/src',
                // This is a relative URI reference and does not have a scheme,
                // see https://tools.ietf.org/html/rfc3986#section-4.1
                null,
                '/opt/data/project/src',
            ],
            'test local file on windows with scheme' => [
                'file:///C:\\phpdocumentor\\tests',
                'file:///C:/phpdocumentor/tests',
                'file',
                'C:/phpdocumentor/tests',
            ],
            'test http port is inferred on git' => [
                'git+http://github.com',
                'git+http://github.com',
                'git+http',
                '/',
                'github.com',
                80,
            ],
            'test http port is inferred for http scheme' => [
                'http://github.com',
                'http://github.com',
                'http',
                '/',
                'github.com',
                80,
            ],
            'test https port is inferred on git' => [
                'git+https://github.com',
                'git+https://github.com',
                'git+https',
                '/',
                'github.com',
                443,
            ],
            'test https port is inferred for https scheme' => [
                'https://github.com',
                'https://github.com',
                'https',
                '/',
                'github.com',
                443,
            ],
            'test relative local file on unix without scheme containing parent dir' => [
                '../project/src',
                '../project/src',
                // This is a relative URI reference and does not have a scheme,
                // see https://tools.ietf.org/html/rfc3986#section-4.1
                null,
                '../project/src',
            ],
        ];
    }

    /**
     * @covers ::resolve
     * @dataProvider resolveDsnProvider
     */
    public function testResolve(string $baseDsn, string $srcDsn, string $expected, string|null $scheme): void
    {
        $baseDsn   = Dsn::createFromString($baseDsn);
        $srcDsn    = Dsn::createFromString($srcDsn);
        $newSrcDns = $srcDsn->resolve($baseDsn);

        $this->assertEquals($expected, (string) $newSrcDns);
        $this->assertEquals($scheme, $newSrcDns->getScheme());
    }

    public static function resolveDsnProvider(): array
    {
        return [
            'Relative src uri level up' => [
                'file:///project/config',
                '../src',
                'file:///project/src',
                'file',
            ],
            'Relative src deeper level' => [
                'file:///project/config',
                './src',
                'file:///project/config/src',
                'file',
            ],
            'Absolute src uri' => [
                'file:///project/config',
                '/src',
                '/src',
                null,
            ],
            'Absolute src full dsn' => [
                'file:///project/config',
                'git+http://user:pw@github.com:8000/phpDocumentor/phpDocumentor2?q=qry1&x=qry2;branch=dev;other=xxx',
                'git+http://user:pw@github.com:8000/phpDocumentor/phpDocumentor2?q=qry1&x=qry2;branch=dev;other=xxx',
                'git+http',
            ],
            'Relative windows uri' => [
                'file:///c:/project/config',
                './src',
                'file:///c:/project/config/src',
                'file',
            ],
            'Relative windows uri without scheme' => [
                'c:/project/config',
                './src',
                'file:///c:/project/config/src',
                'file',
            ],
//                'Relative path in git dsn with parameters' => [
//                    'git+http://user:pw@github.com:8000/phpDocumentor/phpDocumentor2?q=qry1&x=qry2;branch=dev;other=xxx',
//                    './src',
//                    'git+http://user:pw@github.com:8000/phpDocumentor/phpDocumentor2/src?q=qry1&x=qry2;branch=dev;other=xxx',
//                ],
        ];
    }
}
