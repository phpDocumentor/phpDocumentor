<?php
abstract class Nabu_Reflection_BracesAbstract extends Nabu_Abstract
{
  public function processTokens(Nabu_TokenIterator $tokens)
  {
    $level = -1;
    $start = 0;
    $end   = 0;

    // parse class contents
    $this->debug('    Parsing tokens');
    $token = null;
    while ($tokens->valid())
    {
      $token = $token === null ? $tokens->current() : $tokens->next();

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
              break 2;
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