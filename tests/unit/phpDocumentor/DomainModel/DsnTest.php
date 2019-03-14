<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2018 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\DomainModel;

use PHPUnit\Framework\TestCase;

/**
 * Class DsnTest
 * @coversDefaultClass \phpDocumentor\DomainModel\Dsn
 */
class DsnTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::<private>
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidDsn()
    {
        $dsn = 'git+http://namé:password@github.com';
        new Dsn($dsn);
    }

    /**
     * @covers ::__construct
     * @covers ::<private>
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidScheme()
    {
        $dsn = 'gittt+http://github.com';
        new Dsn($dsn);
    }

    /**
     * @covers ::__construct
     * @covers ::getScheme
     * @covers ::<private>
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidKeyValuePair()
    {
        $dsn = 'git+http://@github.com/phpDocumentor/phpDocumentor2?q+query';
        new Dsn($dsn);
    }

    /**
     * @covers ::__construct
     * @covers ::__toString
     * @covers ::getScheme
     * @covers ::getUsername
     * @covers ::getPassword
     * @covers ::getPort
     * @covers ::getHost
     * @covers ::getPath
     * @covers ::getQuery
     * @covers ::getParameters
     * @covers ::<private>
     * @uses \phpDocumentor\DomainModel\Path
     */
    public function testValidDsnWithScheme()
    {
        $dsn = 'git+http://user:pw@github.com:8000/phpDocumentor/phpDocumentor2?q=qry1&x=qry2;branch=dev;other=xxx';
        $fixture = new Dsn($dsn);
        $query = [
            'q' => 'qry1',
            'x' => 'qry2',
        ];

        $parameters = [
            'branch' => 'dev',
            'other' => 'xxx',
        ];

        $this->assertEquals($dsn, (string) $fixture);
        $this->assertEquals('git+http', $fixture->getScheme());
        $this->assertEquals('user', $fixture->getUsername());
        $this->assertEquals('pw', $fixture->getPassword());
        $this->assertEquals(8000, $fixture->getPort());
        $this->assertEquals('github.com', $fixture->getHost());
        $this->assertEquals('/phpDocumentor/phpDocumentor2', $fixture->getPath());
        $this->assertEquals($query, $fixture->getQuery());
        $this->assertEquals($parameters, $fixture->getParameters());
    }

    /**
     * @covers ::__construct
     * @covers ::__toString
     * @covers ::getScheme
     * @covers ::getHost
     * @covers ::getPort
     * @covers ::getPath
     * @covers ::<private>
     * @uses \phpDocumentor\DomainModel\Path
     */
    public function testValidDsnWithoutScheme()
    {
        $dsn = 'src';
        $fixture = new Dsn($dsn);

        $this->assertEquals('file://src', (string) $fixture);
        $this->assertEquals('file', $fixture->getScheme());
        $this->assertEquals(null, $fixture->getHost());
        $this->assertEquals(0, $fixture->getPort());
        $this->assertEquals('src', $fixture->getPath());
    }

    /**
     * @covers ::__construct
     * @covers ::__toString
     * @covers ::getScheme
     * @covers ::getHost
     * @covers ::getPort
     * @covers ::getPath
     * @covers ::<private>
     * @uses \phpDocumentor\DomainModel\Path
     */
    public function testValidWindowsDsnWithoutScheme()
    {
        $dsn = 'C:\\phpdocumentor\\tests\\unit\\phpDocumentor\\Infrastructure\\Parser';
        $fixture = new Dsn($dsn);

        $this->assertEquals(
            'file://C:\\phpdocumentor\\tests\\unit\\phpDocumentor\\Infrastructure\\Parser',
            (string) $fixture
        );
        $this->assertEquals('file', $fixture->getScheme());
        $this->assertEquals(null, $fixture->getHost());
        $this->assertEquals(0, $fixture->getPort());
        $this->assertEquals(
            'C:\\phpdocumentor\\tests\\unit\\phpDocumentor\\Infrastructure\\Parser',
            $fixture->getPath()
        );
    }

    /**
     * @covers ::__construct
     * @covers ::__toString
     * @covers ::getScheme
     * @covers ::getHost
     * @covers ::getPort
     * @covers ::getPath
     * @covers ::<private>
     * @uses \phpDocumentor\DomainModel\Path
     */
    public function testValidWindowsDsnWithScheme()
    {
        $dsn = 'file://C:\\phpdocumentor\\tests';
        $fixture = new Dsn($dsn);

        $this->assertEquals('file://C:\\phpdocumentor\\tests', (string) $fixture);
        $this->assertEquals('file', $fixture->getScheme());
        $this->assertEquals(null, $fixture->getHost());
        $this->assertEquals(0, $fixture->getPort());
        $this->assertEquals('C:\\phpdocumentor\\tests', $fixture->getPath());
    }

    /**
     * @covers ::__construct
     * @covers ::getScheme
     * @covers ::getPort
     * @covers ::<private>
     */
    public function testCorrectDefaultPorts()
    {
        $dsn = 'git+http://github.com';
        $fixture = new Dsn($dsn);
        $this->assertEquals(80, $fixture->getPort());

        $dsn = 'git+https://github.com';
        $fixture = new Dsn($dsn);
        $this->assertEquals(443, $fixture->getPort());
    }
}
