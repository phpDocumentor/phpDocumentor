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

namespace phpDocumentor\DomainModel\Parser\Version;

use phpDocumentor\DomainModel\Parser\Version\Definition;
use phpDocumentor\DomainModel\Parser\Version\Factory;
use phpDocumentor\DomainModel\Parser\Version\Number;
use phpDocumentor\DomainModel\Parser\Version;

/**
 * Test case for Factory
 * @coversDefaultClass phpDocumentor\DomainModel\Parser\Version\Factory
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Factory
     */
    private $fixture;

    protected function setUp()
    {
        $this->fixture = new Factory();
    }

    /**
     * @covers ::create
     */
    public function testCreate()
    {
        $versionDefinition = new Definition(new Number('1.0.0'));
        $version = $this->fixture->create($versionDefinition);

        $this->assertInstanceOf(Version::class, $version);
        $this->assertEquals(new Version(new Number('1.0.0')), $version);
    }
}
