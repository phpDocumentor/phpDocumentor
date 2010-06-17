<?php
class Nabu_Reflection_Method extends Nabu_Abstract
{
  protected $name       = '';
  protected $doc_block  = null;
  protected $abstract   = false;
  protected $final      = false;
  protected $static     = false;
  protected $visibility = 'public';
  protected $arguments_token_start = 0;
  protected $arguments_token_end   = 0;

  protected $arguments     = array();

  public function parseTokenizer(Nabu_TokenIterator $tokens)
  {
    $this->debug('  Started to parse method');
    $tokens_time = microtime(true);

    // retrieve generic information about the class
    $this->name       = $tokens->findNextByType(T_STRING, 5, array('{'))->getContent();
    $this->debug('  Name of method is: '.$this->name);

    $tokens_time    = microtime(true);
    $docblock       = $tokens->findPreviousByType(T_DOC_COMMENT, 10, array('{'. '}', ';'));
    $this->docBlock = $docblock ? new Zend_Reflection_Docblock($docblock->getContent()) : '';
    $tokens_elapsed = microtime(true) - $tokens_time;
    $this->debug('    parsed docblock in '.$tokens_elapsed.' seconds');

    $this->isStatic   = $tokens->findPreviousByType(T_STATIC, 5, array('{', ';')) ? true : false;
    $this->isAbstract = $tokens->findPreviousByType(T_ABSTRACT, 5, array('}')) ? true : false;
    $this->isFinal    = $tokens->findPreviousByType(T_FINAL, 5, array('}')) ? true : false;

    $tokens_elapsed = microtime(true) - $tokens_time;
    $this->debug('    determined generic properties in '.$tokens_elapsed.' seconds');

    $tokens_time = microtime(true);

    // register the start and end of this class
    list($start_index, $end_index) = $tokens->getTokenIdsOfBracePair();
    $this->token_start = $start_index;
    $this->token_end = $end_index;

    list($start_index, $end_index) = $tokens->getTokenIdsOfParenthesisPair();
    $this->arguments_token_start = $start_index;
    $this->arguments_token_end   = $end_index;

    $tokens_elapsed = microtime(true) - $tokens_time;
    $this->debug('    determined argument and method range token ids in '.$tokens_elapsed.' seconds');

    // parse class contents
    $this->debug('    Parsing method contents from token #'.$tokens->key().' to #'.$this->getEndTokenId());
    $tokens_time = microtime(true);
    while ($tokens->valid() && $tokens->key() <= $this->getEndTokenId())
    {
      $token = $tokens->current();
      switch ($token->getType())
      {
        case T_VARIABLE:
          // is the variable occurs within arguments parenthesis then it is an argument
          if (($tokens->key() > $this->arguments_token_start) && ($tokens->key() < $this->arguments_token_end))
          {
            $time = microtime(true);
            $argument = new Nabu_Reflection_Argument();
            $argument->parseTokenizer($tokens);
            $this->arguments[$argument->getName()] = $argument;
            $elapsed = microtime(true) - $time;
            $this->debug('    Processed argument '.$argument->getName().' in '.$elapsed.' seconds');
            break;
          }
          break;
        case '':
          $this->log('METHOD: Literal encountered ('.$tokens->key().'): '.$token->getContent());
          break;
        default:
          $this->log('METHOD: Unhandled token encountered ('.$tokens->key().'): '.$token->getName().(($token->getType() == T_STRING) ? ': '.$token->getContent() : ''), Zend_Log::WARN);
      }

      $tokens->next();
    }
    $tokens_elapsed = microtime(true) - $tokens_time;
    $this->debug('    Processed all tokens in '.$tokens_elapsed.' seconds');
  }

  public function getName()
  {
    return $this->name;
  }

  public function getDocBlock()
  {
    return $this->doc_block;
  }

  public function getVisibility()
  {
    return $this->visibility;
  }

  public function isAbstract()
  {
    return $this->abstract;
  }

  public function isStatic()
  {
    return $this->static;
  }

  public function isFinal()
  {
    return $this->final;
  }

  public function __toString()
  {
    return $this->getName();
  }

  public function __toXml()
  {
    $xml = new SimpleXMLElement('<method></method>');
    $xml->name         = $this->getName();
    $xml['final']      = $this->isFinal() ? 'true' : 'false';
    $xml['abstract']   = $this->isAbstract() ? 'true' : 'false';
    $xml['static']   = $this->isStatic() ? 'true' : 'false';
    $xml['visibility'] = $this->getVisibility();

    $dom = new DOMDocument('1.0', 'UTF-8');
    $dom->loadXML($xml->asXML());

    // import methods into class xml
    foreach ($this->arguments as $argument)
    {
      $dom_arguments = new DOMDocument();
      $dom_arguments->loadXML(trim($argument->__toXml()));

      $xpath = new DOMXPath($dom_arguments);
      $qry = $xpath->query('/*');
      for ($i = 0; $i < $qry->length; $i++)
      {
        $dom->documentElement->appendChild($dom->importNode($qry->item($i), true));
      }
    }

    return trim($dom->saveXML());
  }
}