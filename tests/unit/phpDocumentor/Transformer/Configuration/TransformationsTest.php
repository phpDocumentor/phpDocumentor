<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Transformer\Configuration;

use Mockery as m;

class TransformationsTest extends \PHPUnit_Framework_TestCase
{
    /** @var Transformations */
    private $fixture;

    /**
     * Initializes the fixture for this test.
     */
    public function setUp()
    {
        $this->fixture = new Transformations(array('template'), array('transformation'));
    }

    /**
     * @covers phpDocumentor\Transformer\Configuration\Transformations::__construct
     * @covers phpDocumentor\Transformer\Configuration\Transformations::getTemplates
     */
    public function testIfAListOfTemplatesCanBeRetrieved()
    {
        $this->assertSame(array('template'), $this->fixture->getTemplates());
    }

    /**
     * @covers phpDocumentor\Transformer\Configuration\Transformations::__construct
     * @covers phpDocumentor\Transformer\Configuration\Transformations::getTransformations
     */
    public function testIfAListOfTransformationsCanBeRetrieved()
    {
        $this->assertSame(array('transformation'), $this->fixture->getTransformations());
    }
}
