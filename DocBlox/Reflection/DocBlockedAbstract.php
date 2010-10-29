<?php
/**
 * @author    mvriel
 * @copyright
 */

/**
 * Abstract base class for all Reflection entities which have a docblock.
 *
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @package    docblox
 * @subpackage reflection
 */
abstract class DocBlox_Reflection_DocBlockedAbstract extends DocBlox_Reflection_Abstract
{
  protected $doc_block = null;

  protected function processGenericInformation(DocBlox_TokenIterator $tokens)
  {
    $this->doc_block = $this->findDocBlock($tokens);
  }

  /**
   * Returns the DocBlock reflection object.
   *
   * @return Zend_Reflection_Docblock
   */
  public function getDocBlock()
  {
    return $this->doc_block;
  }

  /**
   * Returns the first docblock preceding the active token within 10 tokens.
   *
   * Please note that the iterator cursor does not change due to this method
   *
   * @param  DocBlox_TokenIterator $tokens
   * @return Zend_Reflection_DocBlock|null
   */
  protected function findDocBlock(DocBlox_TokenIterator $tokens)
  {
    $result = null;
    $docblock = $tokens->findPreviousByType(T_DOC_COMMENT, 10, array('{'. '}', ';'));
    try
    {
      $result = $docblock ? new Zend_Reflection_Docblock($docblock->getContent()) : null;
      if ($result)
      {
        // attach line number to class, the Zend_Reflection_DocBlock does not know the number
        $result->line_number = $docblock->getLineNumber();
      }
    }
    catch (Exception $e)
    {
      $this->log($e->getMessage(), Zend_Log::CRIT);
    }

    if (!$result)
    {
      $this->log('No DocBlock was found for '.substr(get_class($this), strrpos(get_class($this), '_')+1).' '.$this->getName().' on line '.$this->getLineNumber(), Zend_Log::ERR);
    }

    return $result;
  }

  protected function addDocblockToSimpleXmlElement(SimpleXMLElement $xml)
  {
    if ($this->getDocBlock())
    {
      if (!isset($xml->docblock))
      {
        $xml->addChild('docblock');
      }
      $xml->docblock->description = utf8_encode(str_replace(PHP_EOL, '<br/>', $this->getDocBlock()->getShortDescription()));
      $xml->docblock->{'long-description'} = utf8_encode(str_replace(PHP_EOL, '<br/>', $this->getDocBlock()->getLongDescription()));

      /** @var Zend_Reflection_Docblock_Tag $tag */
      foreach ($this->getDocBlock()->getTags() as $tag)
      {
        $tag_object = $xml->docblock->addChild('tag', utf8_encode(htmlspecialchars($tag->getDescription())));
        $tag_object['name'] = trim($tag->getName(), '@');

        if (method_exists($tag, 'getType'))
        {
          $tag_object['type'] = $tag->getType();
        }

        if (method_exists($tag, 'getVariableName'))
        {
          $tag_object['variable'] = $tag->getVariableName();
        }

        // custom attached member variable, see line 51
        if (isset($this->getDocBlock()->line_number))
        {
          $tag_object['line'] = $this->getDocBlock()->line_number;
        }
      }
    }
  }

}
