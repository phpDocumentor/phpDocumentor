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
    public function testGenerallyInvalidDsn()
    {
        $dsn = "git+http://name:password@github.com:a/";
        new Dsn($dsn);
    }

    /**
     * @covers ::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidScheme()
    {
        $dsn = "gittt+http://github.com";
        new Dsn($dsn);
    }

    /**
     * @covers ::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidUser()
    {
        $dsn = "git+http://user<@github.com";
        new Dsn($dsn);
    }

    /**
     * @covers ::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidPassword()
    {
        $dsn = "git+http://user:pass<word@github.com";
        new Dsn($dsn);
    }

    /**
     * @covers ::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidHost()
    {
        $dsn = "git+http://github+com";
        new Dsn($dsn);
    }

    /**
     * @covers ::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidPath()
    {
        $dsn = "git+http://@github.com/phpDocu<mentor";
        new Dsn($dsn);
    }

    /**
     * @covers ::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidQuery()
    {
        $dsn = "git+http://@github.com/phpDocumentor/phpDocumentor2?q=que<ry";
        new Dsn($dsn);
    }

    /**
     * @covers ::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidParameter()
    {
        $dsn = "git+http://@github.com/phpDocumentor/phpDocumentor2?q=query;branch=deve<lop";
        new Dsn($dsn);
    }

    /**
     * @covers ::getScheme
     * @covers ::getUsername
     * @covers ::getPassword
     * @covers ::getPort
     * @covers ::getHost
     * @covers ::getPath
     * @covers ::getQuery
     * @covers ::getParameters
     */
    public function testValidDsn()
    {
        $dsn ="git+http://user:pw@github.com:8000/phpDocumentor/phpDocumentor2?q=qry1?q=qry2;branch=dev;other=xxx";
        $fixture = new Dsn($dsn);
        $query = [
            0 => 'q=qry1',
            1 => 'q=qry2'
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
     */
    public function testNoSchemeIsFile()
    {
        $dsn = "src";
        $fixture = new Dsn($dsn);

        $this->assertEquals('src', $fixture->getPath());
        $this->assertEquals('file', $fixture->getscheme());
        $this->assertEquals(null, $fixture->getHost());
    }

    /**
     * @covers ::__construct
     */
    public function testSchemeIsFile()
    {
        $dsn = "file://src";
        $fixture = new Dsn($dsn);

        $this->assertEquals('src', $fixture->getPath());
        $this->assertEquals('file', $fixture->getscheme());
        $this->assertEquals(null, $fixture->getHost());
    }
}
