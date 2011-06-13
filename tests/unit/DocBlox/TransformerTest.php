<?php
/**
 * DocBlox
 *
 * PHP Version 5
 *
 * @category   DocBlox
 * @package    Transformer
 * @subpackage Tests
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @copyright  2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */

/**
 * Test class for DocBlox_Transformer.
 *
 * @category   DocBlox
 * @package    Transformer
 * @subpackage Tests
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */
class DocBlox_TransformerTest extends PHPUnit_Framework_TestCase
{
    /** @var DocBlox_Transformer */
    protected $fixture = null;

    /**
     * Instantiates a new DocBlox_Transformer for use as fixture.
     *
     * @return void
     */
    protected function setUp()
    {
        $this->fixture = new DocBlox_Transformer();
    }

    /**
     * Tests whether setting a target succeed.
     *
     * @return void
     */
    public function testTarget()
    {
        $this->assertEquals('', $this->fixture->getTarget());

        $this->fixture->setTarget(dirname(__FILE__));
        $this->assertEquals(dirname(__FILE__), $this->fixture->getTarget());

        // only directories are accepted, not files
        $this->setExpectedException('Exception');
        $this->fixture->setTarget(__FILE__);

        // only valid directories are accepted
        $this->setExpectedException('Exception');
        $this->fixture->setTarget(dirname(__FILE__) . 'a');
    }

    /**
     * Tests whether setting the source succeeds.
     *
     * @return void
     */
    public function testSource()
    {
        $this->assertEquals('', $this->fixture->getSource());
        file_put_contents('/tmp/test_structure.xml', '<structure></structure>');

        $this->fixture->setSource('/tmp/test_structure.xml');
        $this->assertInstanceOf('DOMDocument', $this->fixture->getSource());

        // directories are not allowed
        $this->setExpectedException('Exception');
        $this->fixture->setSource('/tmp');

        // unknown directories are not allowed
        $this->setExpectedException('Exception');
        $this->fixture->setSource('/tmpa');

        $this->markTestIncomplete(
            'We still need to test the structure.xml changes that are induced '
            . 'by the addMetaDataToStructure method'
        );
    }

    /**
     * Tests whether getting and setting the template name works.
     *
     * @return void
     */
    public function testTemplate()
    {
        $this->assertSame(array('default'), $this->fixture->getTemplates());

        DocBlox_Core_Abstract::config()->templates->test = new Zend_Config(array());
        DocBlox_Core_Abstract::config()->templates->test2 = new Zend_Config(array());

        $this->fixture->setTemplates('test');
        $this->assertEquals(array('test'), $this->fixture->getTemplates());

        $this->fixture->setTemplates(array('test', 'test2'));
        $this->assertEquals(array('test', 'test2'), $this->fixture->getTemplates());
    }

    /**
     * Tests whether getting and setting transformations work.
     *
     * @return void
     */
    public function testTransformations()
    {
        $this->assertNotEmpty($this->fixture->getTransformations());
        $count = count($this->fixture->getTransformations());

        // test creation without parameters
        $this->fixture->addTransformation(
            array(
                 'query' => 'a',
                 'writer' => 'Xsl',
                 'source' => 'b',
                 'artifact' => 'c',
            )
        );
        $this->assertEquals(
            $count + 1,
            count($this->fixture->getTransformations())
        );
        $this->assertInstanceOf(
            'DocBlox_Transformer_Transformation',
            current($this->fixture->getTransformations())
        );
        $this->assertEquals(
            0,
            count(current($this->fixture->getTransformations())->getParameters())
        );

        // test creation with parameters
        $this->fixture->addTransformation(
            array(
                 'query' => 'a',
                 'writer' => 'Xsl',
                 'source' => 'b',
                 'artifact' => 'c',
                 'parameters' => array(
                     '1' => '2'
                 )
            )
        );
        $this->assertEquals($count + 2, count($this->fixture->getTransformations()));
        $transformations = $this->fixture->getTransformations();
        $this->assertEquals(1, count($transformations[$count + 1]->getParameters()));

        // test creation with pre-fab object
        $transformation = new DocBlox_Transformer_Transformation(
            $this->fixture, 'd', 'Xsl', 'b', 'c'
        );
        $this->fixture->addTransformation($transformation);

        $this->assertEquals($count + 3, count($this->fixture->getTransformations()));

        try
        {
            $this->fixture->addTransformation(array('a' => 'b'));
            $this->fail(
                'Expected an exception to be thrown when '
                . 'supplying a bogus array'
            );
        }
        catch (Exception $e)
        {
            // this is good; exception is thrown
        }

        try
        {
            $this->fixture->addTransformation('a');
            $this->fail('Expected an exception when supplying a scalar');
        }
        catch (Exception $e)
        {
            // this is good; exception is thrown
        }

        try
        {
            $this->fixture->addTransformation(new StdClass());
            $this->fail('Expected an exception when supplying a false object');
        }
        catch (Exception $e)
        {
            // this is good; exception is thrown
        }
    }

    /**
     * Tests whether adding a template has the desired effect.
     *
     * @return void
     */
    public function testAddTemplate()
    {
        $this->assertNotEmpty(
            $this->fixture->getTransformations(),
            'Initial state should not be an empty array'
        );
        $this->fixture->addTemplate('default');

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
        catch (InvalidArgumentException $e)
        {
            // this is good; exception is thrown
        }
        catch (Exception $e)
        {
            $this->fail(
                'An unknown exception has occurred when supplying a '
                . 'non-existent template: ' . $e->getMessage()
            );
        }

        // nothing should happen when template does not contain transformations
        DocBlox_Core_Abstract::config()->templates->wargarbl = '';

        $count = count($this->fixture->getTransformations());
        $this->assertNull(
            $this->fixture->addTemplate('wargarbl'),
            'No erroneous actions should happen when adding an empty template'
        );
        $this->assertEquals(
            $count,
            count($this->fixture->getTransformations()),
            'Transformation count should be unchanged'
        );
    }

    /**
     * Tests whether the generateFilename method returns a file according to
     * the right format.
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
            '_directory_directory2_file.html',
            $this->fixture->generateFilename($filename)
        );
    }

    /**
     * Tests whether the loading of transformation is working as expected.
     *
     * @return void
     */
    public function testLoadTransformations()
    {
        DocBlox_Core_Abstract::config()->transformations = array();

        $this->fixture = new DocBlox_Transformer();
        $this->assertEquals(
            0,
            count($this->fixture->getTransformations()),
            'A transformer should not have transformations when initialized '
            . 'with an empty configuration'
        );

        DocBlox_Core_Abstract::config()->transformations = array(
            new Zend_Config_Xml(
<<<XML
<?xml version="1.0"?>
<transformation>
  <query>test1</query>
  <writer>Xsl</writer>
  <source>test3</source>
  <artifact>test4</artifact>
</transformation>
XML
            ));

        $this->fixture->loadTransformations();
        $this->assertEquals(
            1,
            count($this->fixture->getTransformations()),
            'When invoking the loadTransformations method with only a single '
            . 'transformation in the configuration there should be only 1'
        );

        // TODO: add test to check if a transformation which is passed as
        // argument is added correctly
        // TODO: add test to check if a template's transformations which is
        // passed as argument is added correctly
        $this->markTestIncomplete();
    }

    /**
     * Tests whether an execute call will yield the desired results.
     *
     * @return void
     */
    public function testExecute()
    {
        DocBlox_Core_Abstract::config()->transformations = array();
        $this->fixture = new DocBlox_Transformer();

        // when nothing is added; this mock should not be invoked
        $transformation = $this->getMock(
            'DocBlox_Transformer_Transformation',
            array('execute'),
            array($this->fixture, '', 'Xsl', '', '')
        );

        $transformation->expects($this->never())->method('execute');
        $this->fixture->execute();

        // when we add this mock; we expect it to be invoked
        $transformation = $this->getMock(
            'DocBlox_Transformer_Transformation',
            array('execute'),
            array($this->fixture, '', 'Xsl', '', '')
        );

        $transformation->expects($this->once())->method('execute');
        $this->fixture->addTransformation($transformation);
        $this->fixture->execute();
    }


}