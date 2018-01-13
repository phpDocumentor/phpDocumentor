<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Transformer\Configuration;

class TransformationsTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    /** @var Transformations */
    private $fixture;

    /**
     * Initializes the fixture for this test.
     */
    public function setUp()
    {
        $this->fixture = new Transformations(['template'], ['transformation']);
    }

    /**
     * @covers phpDocumentor\Transformer\Configuration\Transformations::__construct
     * @covers phpDocumentor\Transformer\Configuration\Transformations::getTemplates
     */
    public function testIfAListOfTemplatesCanBeRetrieved()
    {
        $this->assertSame(['template'], $this->fixture->getTemplates());
    }

    /**
     * @covers phpDocumentor\Transformer\Configuration\Transformations::__construct
     * @covers phpDocumentor\Transformer\Configuration\Transformations::getTransformations
     */
    public function testIfAListOfTransformationsCanBeRetrieved()
    {
        $this->assertSame(['transformation'], $this->fixture->getTransformations());
    }
}
