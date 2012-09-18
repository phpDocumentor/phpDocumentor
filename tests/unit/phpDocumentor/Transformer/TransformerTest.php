<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2012 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Transformer;

/**
 * Test class for \phpDocumentor\Transformer\Transformer.
 */
class TransformerTest extends \PHPUnit_Framework_TestCase
{
    /** @var Transformer */
    protected $fixture = null;

    /**
     * Instantiates a new \phpDocumentor\Transformer for use as fixture.
     *
     * @return void
     */
    protected function setUp()
    {
        $this->fixture = new Transformer();
    }

    /**
     * Tests whether setting a target succeed.
     *
     * @covers phpDocumentor\Transformer\Transformer::getTarget
     * @covers phpDocumentor\Transformer\Transformer::setTarget
     *
     * @return void
     */
    public function testTarget()
    {
        $this->assertEquals('', $this->fixture->getTarget());

        $this->fixture->setTarget(dirname(__FILE__));
        $this->assertEquals(dirname(__FILE__), $this->fixture->getTarget());

        // only directories are accepted, not files
        $this->setExpectedException('\InvalidArgumentException');
        $this->fixture->setTarget(__FILE__);

        // only valid directories are accepted
        $this->setExpectedException('\InvalidArgumentException');
        $this->fixture->setTarget(dirname(__FILE__) . 'a');
    }

    /**
     * Tests whether setting the source succeeds.
     *
     * @covers phpDocumentor\Transformer\Transformer::getSource
     * @covers phpDocumentor\Transformer\Transformer::setSource
     *
     * @return void
     */
    public function testSource()
    {
        $this->assertEquals('', $this->fixture->getSource());
        file_put_contents('/tmp/test_structure.xml', '<structure></structure>');

        $this->fixture->setSource('/tmp/test_structure.xml');
        $this->assertInstanceOf('\DOMDocument', $this->fixture->getSource());

        // directories are not allowed
        $this->setExpectedException('\InvalidArgumentException');
        $this->fixture->setSource('/tmp');

        // unknown directories are not allowed
        $this->setExpectedException('\InvalidArgumentException');
        $this->fixture->setSource('/tmpa');

        $this->markTestIncomplete(
            'We still need to test the structure.xml changes that are induced '
            . 'by the addMetaDataToStructure method'
        );
    }

    /**
     * Tests whether adding a template has the desired effect.
     *
     * @covers phpDocumentor\Transformer\Transformer::addTemplate
     * @covers phpDocumentor\Transformer\Transformer::getTransformations
     *
     * @return void
     */
    public function testAddTemplate()
    {
        $this->fixture->setTemplatesPath(
            dirname(__FILE__).'/../../../../data/templates'
        );
        $this->fixture->addTemplate('responsive');

        $this->assertGreaterThan(
            0,
            count($this->fixture->getTransformations()),
            'Transformations should be added'
        );

        try
        {
            $this->fixture->addTemplate('wargarbl');
            $this->fail(
                'Expected an exception to be thrown when '
                . 'supplying a non-existent template'
            );
        }
        catch (\InvalidArgumentException $e)
        {
            // this is good; exception is thrown
        }
        catch (\Exception $e)
        {
            $this->fail(
                'An unknown exception has occurred when supplying a '
                . 'non-existent template: ' . $e->getMessage()
            );
        }
    }

    /**
     * Tests whether the generateFilename method returns a file according to
     * the right format.
     *
     * @covers phpDocumentor\Transformer\Transformer::generateFilename
     *
     * @return void
     */
    public function testGenerateFilename()
    {
        // separate the directories with the DIRECTORY_SEPARATOR constant to
        // prevent failing tests on windows
        $filename = 'directory' . DIRECTORY_SEPARATOR . 'directory2'
            . DIRECTORY_SEPARATOR . 'file.php';
        $this->assertEquals(
            'directory.directory2.file.html',
            $this->fixture->generateFilename($filename)
        );
    }

}