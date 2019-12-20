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

use PHPUnit\Framework\TestCase;

/**
 * Class DsnTest
 *
 * @coversDefaultClass \phpDocumentor\Dsn
 * @covers ::__construct
 * @covers ::<private>
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
     *
     * @dataProvider provideDsnsToTestAgainst
     */
    public function testValidDsnWithScheme(
        string $dsn,
        string $normalizedDsn,
        ?string $scheme,
        string $path = '',
        string $host = '',
        ?int $port = null,
        string $user = '',
        string $pass = '',
        array $query = [],
        array $parameters = []
    ) : void {
        $fixture = new Dsn($dsn);

        $this->assertSame($normalizedDsn, (string) $fixture);
        $this->assertSame($scheme, $fixture->getScheme());
        $this->assertSame($user, $fixture->getUsername());
        $this->assertSame($pass, $fixture->getPassword());
        $this->assertSame($port, $fixture->getPort());
        $this->assertSame($host, $fixture->getHost());
        $this->assertEquals(new Path($path), $fixture->getPath());
        $this->assertSame($query, $fixture->getQuery());
        $this->assertSame($parameters, $fixture->getParameters());
    }

    public function provideDsnsToTestAgainst() : array
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
            'test local file on windows without scheme' => [
                'C:\\phpdocumentor\\tests\\unit\\phpDocumentor\\Parser',
                'file:///C:/phpdocumentor/tests/unit/phpDocumentor/Parser',
                'file',
                '/C:/phpdocumentor/tests/unit/phpDocumentor/Parser',
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
                'file:///C:\\phpdocumentor\\tests',
                'file',
                '/C:\\phpdocumentor\\tests',
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
        ];
    }
}
