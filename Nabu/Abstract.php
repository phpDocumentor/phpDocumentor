<?php
/**
 * @author    mvriel
 * @copyright
 */

/**
 * Provide a short description for this class.
 *
 * @author     mvriel
 * @package
 * @subpackage
 */
abstract class Nabu_Abstract
{
  static protected $logger = null;
  static protected $debug_logger = null;

  protected $token_start = 0;
  protected $token_end   = 0;

  public function getStartTokenId()
  {
    return $this->token_start;
  }

  public function getEndTokenId()
  {
    return $this->token_end;
  }

  protected function debug($message)
  {
    if (!self::$debug_logger)
    {
      // TODO convert to loading from config
      self::$debug_logger = new Zend_Log(new Zend_Log_Writer_Stream(fopen('debug.log', 'w')));
    }

    $dump = $message;

    if (!is_string($dump))
    {
      ob_start();
      var_dump($message);
      $dump = ob_get_clean();
    }

    self::$debug_logger->log($dump, Zend_Log::DEBUG);
  }

  public function log($message, $priority = Zend_Log::INFO)
  {
    if ($priority == Zend_Log::DEBUG)
    {
      $this->debug($message);
      return;
    }

    if (!self::$logger)
    {
      // TODO convert to loading from config
      self::$logger = new Zend_Log(new Zend_Log_Writer_Stream(fopen('errors.log', 'w')));
    }

    self::$logger->log($message, $priority);
  }
}
