<?php
/**
* Test class for FileIO writer.
*/
class DocBlox_Transformer_Writer_FileIoTest extends PHPUnit_Framework_TestCase
{
  /** @var DocBlox_Transformer_Writer_FileIo */
  protected $fixture = null;

  protected function setUp()
  {
    $this->fixture = new DocBlox_Plugin_Core_Transformer_Writer_FileIo();
  }

  public function testExecuteQueryCopy()
  {
    touch('/tmp/docblox_a');
    @unlink('/tmp/docblox_b');
    $this->assertFileExists('/tmp/docblox_a');
    $this->assertFileNotExists('/tmp/docblox_b');

    $tr = new DocBlox_Transformer();
    $tr->setTarget('/tmp');
    try
    {
      $t = new DocBlox_Transformer_Transformation($tr, 'copy', 'FileIo', '/tmp/docblox_b', 'docblox_c');
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
      $t = new DocBlox_Transformer_Transformation($tr, 'copy', 'FileIo', '/tmp/docblox_a', 'docblox_b');
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
    $t = new DocBlox_Transformer_Transformation($tr, 'copy', 'FileIo', '/tmp/docblox_a', '/tmp/docblox_b');
    $this->fixture->executeQueryCopy($t);
    $this->assertFileExists('/tmp/docblox_a');
    $this->assertFileExists('/tmp/docblox_b');
    unlink('/tmp/docblox_a');
    unlink('/tmp/docblox_b');
  }

  public function testExecuteTransform()
  {
    touch('/tmp/docblox_a');
    @unlink('/tmp/docblox_b');
    $this->assertFileExists('/tmp/docblox_a');
    $this->assertFileNotExists('/tmp/docblox_b');

    $tr = new DocBlox_Transformer();
    $tr->setTarget('/tmp');

    try
    {
      $t = new DocBlox_Transformer_Transformation($tr, 'copyz', 'FileIo', '/tmp/docblox_a', 'docblox_b');
      $this->fixture->transform(new DOMDocument(), $t);

      $this->fail('When un unknown query type is used an exception is expected');
    } catch(InvalidArgumentException $e)
    {
      // this is good
    }

    $t = new DocBlox_Transformer_Transformation($tr, 'copy', 'FileIo', '/tmp/docblox_a', 'docblox_b');
    $this->fixture->transform(new DOMDocument(), $t);
    $this->assertFileExists('/tmp/docblox_a');
    $this->assertFileExists('/tmp/docblox_b');
    unlink('/tmp/docblox_a');
    unlink('/tmp/docblox_b');
  }
}