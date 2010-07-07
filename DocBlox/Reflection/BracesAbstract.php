<?php
abstract class DocBlox_Reflection_BracesAbstract extends DocBlox_Reflection_Abstract
{
  public function processTokens(DocBlox_TokenIterator $tokens)
  {
    $level = -1;
    $start = 0;
    $end   = 0;

    // parse class contents
    $this->debug('>> Processing tokens');
    $token = null;
    while ($tokens->valid())
    {
      $token = $token === null ? $tokens->current() : $tokens->next();

      // determine where the 'braced' section starts and end.
      // the first open brace encountered is considered the opening brace for the block and processing will
      // be 'breaked' when the closing brace is encountered
      if ($token && !$token->getType() && (($token->getContent() == '{') || (($token->getContent() == '}'))))
      {
        switch ($token->getContent())
        {
          case '{':
            if ($level == -1)
            {
              $level++;
              $start = $tokens->key();
            }
            $level++;
            break;
          case '}':
            // expect the first brace to be an opening brace
            if ($level == -1) continue;
            $level--;

            // reached the end; break from the while
            if ($level === 0)
            {
              $end = $tokens->key();
              break 2; // time to say goodbye
            }
            break;
        }
        continue;
      }

      if ($token && $token->getType())
      {
        $this->processToken($token, $tokens);
      }
    }

    return array($start, $end);
  }

}