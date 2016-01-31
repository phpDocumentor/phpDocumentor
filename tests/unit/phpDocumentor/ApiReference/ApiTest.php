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

namespace phpDocumentor\ApiReference;

use Mockery as m;
use phpDocumentor\DomainModel\Documentation\Api\Api;
use phpDocumentor\DomainModel\Documentation\DocumentGroup\DocumentGroupFormat;
use phpDocumentor\Reflection\Element;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Php\Project;

/**
 * Test case for Api
 * @coversDefaultClass phpDocumentor\ApiReference\Api
 * @covers ::__construct
 */
class ApiTest extends \PHPUnit_Framework_TestCase
{
    /** @var Api */
    private $api;

    /** @var DocumentGroupFormat */
    private $documentGroupFormat;

    /** @var Project */
    private $project;

    /**
     * Initializes this Reference object for API elements.
     */
    public function setUp()
    {
        $this->documentGroupFormat = new DocumentGroupFormat('api');
        $this->project             = new Project('MyProject');
        $this->api                 = new Api($this->documentGroupFormat, $this->project);
    }

    /**
     * @covers ::getFormat
     */
    public function testGetFormat()
    {
        $this->assertSame($this->documentGroupFormat, $this->api->getFormat());
    }

    /**
     * @covers ::addElement
     * @covers ::findElementByFqsen
     */
    public function testAddingAndRetrievingAnElement()
    {
        $fqsen   = new Fqsen('\\My\\Space');
        $element = $this->givenAnElementWithFqsen($fqsen);

        $this->api->addElement($element);
        $this->assertSame($element, $this->api->findElementByFqsen($fqsen));
    }

    /**
     * @covers ::findElementByFqsen
     */
    public function testNullIsReturnedWhenFindingAnUnknownElement()
    {
        $this->assertNull($this->api->findElementByFqsen(new Fqsen('\My\Class')));
    }

    /**
     * @covers ::getElements
     */
    public function testRetrievingAllElementsReturnsAListOfElements()
    {
        $fqsenString = '\\My\\Space';
        $element = $this->givenAnElementWithFqsen(new Fqsen($fqsenString));

        $this->api->addElement($element);
        $this->assertSame([$fqsenString => $element], $this->api->getElements());
    }

    /**
     * Returns a mocked Element that will return the given FQSEN when asked for.
     *
     * @param Fqsen $fqsen
     *
     * @return m\MockInterface|Element
     */
    private function givenAnElementWithFqsen($fqsen)
    {
        return m::mock(Element::class)
            ->shouldReceive('getFqsen')->andReturn($fqsen)
            ->getMock();
    }
}
