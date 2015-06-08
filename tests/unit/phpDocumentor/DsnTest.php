<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.5
 *
 * @copyright 2010-2015 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor;

/**
 * Class DsnTest
 * @coversDefaultClass phpDocumentor\Dsn
 */
class DsnTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::parse
     * @expectedException \InvalidArgumentException
     */
    public function testDsnIsNotAString()
    {
        $dsn = 1;
        new Dsn($dsn);
    }

    /**
     * @covers ::__construct
     * @covers ::parse
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidDsn()
    {
        $dsn = "git+http://namé:password@github.com";
        new Dsn($dsn);
    }

    /**
     * @covers ::__construct
     * @covers ::parse
     * @covers ::parseScheme
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidScheme()
    {
        $dsn = "gittt+http://github.com";
        new Dsn($dsn);
    }

    /**
     * @covers ::__construct
     * @covers ::getScheme
     * @covers ::parse
     * @covers ::parseScheme
     * @covers ::parseHostAndPath
     * @covers ::parsePort
     * @covers ::parseQuery
     * @covers ::splitKeyValuePair
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidKeyValuePair()
    {
        $dsn = "git+http://@github.com/phpDocumentor/phpDocumentor2?q+query";
        new Dsn($dsn);
    }

    /**
     * @covers ::__construct
     * @covers ::getScheme
     * @covers ::getUsername
     * @covers ::getPassword
     * @covers ::getPort
     * @covers ::getHost
     * @covers ::getPath
     * @covers ::getQuery
     * @covers ::getParameters
     * @covers ::parse
     * @covers ::parseScheme
     * @covers ::parseHostAndPath
     * @covers ::parsePort
     * @covers ::parseQuery
     * @covers ::parseParameters
     * @covers ::splitKeyValuePair
     * @uses phpDocumentor\Path
     */
    public function testValidDsnWithScheme()
    {
        $dsn ="git+http://user:pw@github.com:8000/phpDocumentor/phpDocumentor2?q=qry1&x=qry2;branch=dev;other=xxx";
        $fixture = new Dsn($dsn);
        $query = [
            'q' => 'qry1',
            'x' => 'qry2'
        ];

        $parameters = [
            'branch' => 'dev',
            'other' => 'xxx'
        ];

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
     * @covers ::getScheme
     * @covers ::getHost
     * @covers ::getPort
     * @covers ::getPath
     * @covers ::parse
     * @covers ::parseScheme
     * @covers ::parseHostAndPath
     * @covers ::parsePort
     * @covers ::parseQuery
     * @covers ::parseParameters
     * @uses phpDocumentor\Path
     */
    public function testValidDsnWithoutScheme()
    {
        $dsn = "src";
        $fixture = new Dsn($dsn);

        $this->assertEquals('file', $fixture->getscheme());
        $this->assertEquals(null, $fixture->getHost());
        $this->assertEquals(0, $fixture->getPort());
        $this->assertEquals('src', $fixture->getPath());
    }

    /**
     * @covers ::__construct
     * @covers ::getScheme
     * @covers ::getPort
     * @covers ::parse
     * @covers ::parseScheme
     * @covers ::parseHostAndPath
     * @covers ::parsePort
     * @covers ::parseQuery
     * @covers ::parseParameters
     */
    public function testCorrectDefaultPorts()
    {
        $dsn ="git+http://github.com";
        $fixture = new Dsn($dsn);
        $this->assertEquals(80, $fixture->getPort());

        $dsn ="git+https://github.com";
        $fixture = new Dsn($dsn);
        $this->assertEquals(443, $fixture->getPort());
    }
}
