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

namespace phpDocumentor\Project\Version;

use phpDocumentor\DomainModel\Version\Definition;
use phpDocumentor\DomainModel\Version\Factory;
use phpDocumentor\DomainModel\Version\Number;
use phpDocumentor\DomainModel\Version;

/**
 * Test case for Factory
 * @coversDefaultClass phpDocumentor\Project\Version\Factory
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
