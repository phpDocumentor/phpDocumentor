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

namespace phpDocumentor\Plugin\Core\Transformer\Writer;

/**
 * Tests whether the File IO writer properly copies the given source files.
 */
class FileIoTest extends \PHPUnit_Framework_TestCase
{
    /** @var FileIo */
    protected $fixture = null;

    /**
     * Creates a new FileIO fixture.
     *
     * @return void
     */
    protected function setUp()
    {
        $this->fixture = new FileIo();
    }

    /**
     * Executes whether the query 'copy' is properly executed in a transformation.
     *
     * @covers phpDocumentor\Plugin\Core\Transformer\Writer\FileIO::transform
     *
     * @throws \Exception|\PHPUnit_Framework_AssertionFailedError
     *
     * @return void
     */
    public function testExecuteQueryCopy()
    {
        touch('/tmp/phpdoc_a');
        @unlink('/tmp/phpdoc_b');
        $this->assertFileExists('/tmp/phpdoc_a');
        $this->assertFileNotExists('/tmp/phpdoc_b');

        $transformer = new \phpDocumentor\Transformer\Transformer();
        $transformer->setTarget('/tmp');
        try
        {
            $transformation = new \phpDocumentor\Transformer\Transformation(
                $transformer, 'copy', 'FileIo', '/tmp/phpdoc_b', 'phpdoc_c'
            );
            $this->fixture->transform(new \DOMDocument(), $transformation);

            $this->fail(
                'When a non-existing source is provided, an exception is expected'
            );
        }
        catch (\PHPUnit_Framework_AssertionFailedError $e)
        {
            throw $e;
        }
        catch (\Exception $e)
        {
            // this is good
        }

        try
        {
            $transformer->setTarget('/tmpz');
            $transformation = new \phpDocumentor\Transformer\Transformation(
                $transformer, 'copy', 'FileIo', '/tmp/phpdoc_a', 'phpdoc_b'
            );
            $this->fixture->transform(new \DOMDocument(), $transformation);

            $this->fail(
                'When a non-existing transformer target is provided, '
                . 'an exception is expected'
            );
        }
        catch (\PHPUnit_Framework_AssertionFailedError $e)
        {
            throw $e;
        }
        catch (\Exception $e)
        {
            // this is good
        }

        $this->markTestIncomplete(
            'Absolute files are no longer supported using the FileIo writer, '
            .'the test code should be adapted'
        );

        unlink('/tmp/phpdoc_a');
        unlink('/tmp/phpdoc_b');
    }

    /**
     * Executes whether the query 'copy' is properly executed in a transformation.
     *
     * @covers phpDocumentor\Plugin\Core\Transformer\Writer\FileIO::transform
     *
     * @throws \Exception|\PHPUnit_Framework_AssertionFailedError
     *
     * @return void
     */
    public function testExecuteTransform()
    {
        touch('/tmp/phpdoc_a');
        @unlink('/tmp/phpdoc_b');
        $this->assertFileExists('/tmp/phpdoc_a');
        $this->assertFileNotExists('/tmp/phpdoc_b');

        $transformer = new \phpDocumentor\Transformer\Transformer();
        $transformer->setTarget('/tmp');

        try
        {
            $transformation = new \phpDocumentor\Transformer\Transformation(
                $transformer, 'copyz', 'FileIo', '/tmp/phpdoc_a', 'phpdoc_b'
            );
            $this->fixture->transform(new \DOMDocument(), $transformation);

            $this->fail(
                'When un unknown query type is used an exception is expected'
            );
        }
        catch (\InvalidArgumentException $e)
        {
            // this is good
        }

        $this->markTestIncomplete(
            'Absolute files are no longer supported using the FileIo writer, '
            .'the test code should be adapted'
        );

        unlink('/tmp/phpdoc_a');
        unlink('/tmp/phpdoc_b');
    }
}