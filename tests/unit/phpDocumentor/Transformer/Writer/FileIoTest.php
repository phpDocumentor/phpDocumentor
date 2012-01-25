<?php
/**
* Test class for FileIO writer.
*/
class phpDocumentor_Transformer_Writer_FileIoTest extends PHPUnit_Framework_TestCase
{
  /** @var phpDocumentor_Transformer_Writer_FileIo */
  protected $fixture = null;

  protected function setUp()
  {
    $this->fixture = new phpDocumentor_Plugin_Core_Transformer_Writer_FileIo();
  }

  public function testExecuteQueryCopy()
  {
    touch('/tmp/phpdoc_a');
    @unlink('/tmp/phpdoc_b');
    $this->assertFileExists('/tmp/phpdoc_a');
    $this->assertFileNotExists('/tmp/phpdoc_b');

    $tr = new phpDocumentor_Transformer();
    $tr->setTarget('/tmp');
    try
    {
      $t = new phpDocumentor_Transformer_Transformation(
          $tr, 'copy', 'FileIo', '/tmp/phpdoc_b', 'phpdoc_c'
      );
      $this->fixture->transform(new DOMDocument(), $t);

      $this->fail('When a non-existing source is provided, an exception is expected');
    }
    catch(PHPUnit_Framework_AssertionFailedError $e)
    {
      throw $e;
    }
    catch (Exception $e)
    {
      // this is good
    }

    try
    {
      $tr->setTarget('/tmpz');
      $t = new phpDocumentor_Transformer_Transformation(
          $tr, 'copy', 'FileIo', '/tmp/phpdoc_a', 'phpdoc_b'
      );
      $this->fixture->transform(new DOMDocument(), $t);

      $this->fail('When a non-existing transformer target is provided, an exception is expected');
    }
    catch (PHPUnit_Framework_AssertionFailedError $e)
    {
      throw $e;
    }
    catch (Exception $e)
    {
      // this is good
    }

    $tr->setTarget('/tmp');
    $t = new phpDocumentor_Transformer_Transformation(
        $tr, 'copy', 'FileIo', '/tmp/phpdoc_a', '/tmp/phpdoc_b'
    );
    $this->fixture->executeQueryCopy($t);
    $this->assertFileExists('/tmp/phpdoc_a');
    $this->assertFileExists('/tmp/phpdoc_b');
    unlink('/tmp/phpdoc_a');
    unlink('/tmp/phpdoc_b');
  }

  public function testExecuteTransform()
  {
    touch('/tmp/phpdoc_a');
    @unlink('/tmp/phpdoc_b');
    $this->assertFileExists('/tmp/phpdoc_a');
    $this->assertFileNotExists('/tmp/phpdoc_b');

    $tr = new phpDocumentor_Transformer();
    $tr->setTarget('/tmp');

    try
    {
      $t = new phpDocumentor_Transformer_Transformation(
          $tr, 'copyz', 'FileIo', '/tmp/phpdoc_a', 'phpdoc_b'
      );
      $this->fixture->transform(new DOMDocument(), $t);

      $this->fail('When un unknown query type is used an exception is expected');
    } catch(InvalidArgumentException $e)
    {
      // this is good
    }

    $t = new phpDocumentor_Transformer_Transformation(
        $tr, 'copy', 'FileIo', '/tmp/phpdoc_a', 'phpdoc_b'
    );
    $this->fixture->transform(new DOMDocument(), $t);
    $this->assertFileExists('/tmp/phpdoc_a');
    $this->assertFileExists('/tmp/phpdoc_b');
    unlink('/tmp/phpdoc_a');
    unlink('/tmp/phpdoc_b');
  }
}