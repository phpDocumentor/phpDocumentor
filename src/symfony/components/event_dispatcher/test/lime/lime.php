<?php

/**
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Unit test library.
 *
 * @package    lime
 * @author     Fabien Potencier <fabien.potencier@gmail.com>
 * @version    SVN: $Id: lime.php 29529 2010-05-19 13:41:48Z fabien $
 */
class lime_test
{
  const EPSILON = 0.0000000001;

  protected $test_nb = 0;
  protected $output  = null;
  protected $results = array();
  protected $options = array();

  static protected $all_results = array();

  public function __construct($plan = null, $options = array())
  {
    // for BC
    if (!is_array($options))
    {
      $options = array('output' => $options);
    }

    $this->options = array_merge(array(
      'force_colors'    => false,
      'output'          => null,
      'verbose'         => false,
      'error_reporting' => false,
    ), $options);

    $this->output = $this->options['output'] ? $this->options['output'] : new lime_output($this->options['force_colors']);

    $caller = $this->find_caller(debug_backtrace());
    self::$all_results[] = array(
      'file'  => $caller[0],
      'tests' => array(),
      'stats' => array('plan' => $plan, 'total' => 0, 'failed' => array(), 'passed' => array(), 'skipped' => array(), 'errors' => array()),
    );

    $this->results = &self::$all_results[count(self::$all_results) - 1];

    null !== $plan and $this->output->echoln(sprintf("1..%d", $plan));

    set_error_handler(array($this, 'handle_error'));
    set_exception_handler(array($this, 'handle_exception'));
  }

  static public function reset()
  {
    self::$all_results = array();
  }

  static public function to_array()
  {
    return self::$all_results;
  }

  static public function to_xml($results = null)
  {
    if (is_null($results))
    {
      $results = self::$all_results;
    }

    $dom = new DOMDocument('1.0', 'UTF-8');
    $dom->formatOutput = true;
    $dom->appendChild($testsuites = $dom->createElement('testsuites'));

    $errors = 0;
    $failures = 0;
    $errors = 0;
    $skipped = 0;
    $assertions = 0;

    foreach ($results as $result)
    {
      $testsuites->appendChild($testsuite = $dom->createElement('testsuite'));
      $testsuite->setAttribute('name', basename($result['file'], '.php'));
      $testsuite->setAttribute('file', $result['file']);
      $testsuite->setAttribute('failures', count($result['stats']['failed']));
      $testsuite->setAttribute('errors', count($result['stats']['errors']));
      $testsuite->setAttribute('skipped', count($result['stats']['skipped']));
      $testsuite->setAttribute('tests', $result['stats']['plan']);
      $testsuite->setAttribute('assertions', $result['stats']['plan']);

      $failures += count($result['stats']['failed']);
      $errors += count($result['stats']['errors']);
      $skipped += count($result['stats']['skipped']);
      $assertions += $result['stats']['plan'];

      foreach ($result['tests'] as $test)
      {
        $testsuite->appendChild($testcase = $dom->createElement('testcase'));
        $testcase->setAttribute('name', $test['message']);
        $testcase->setAttribute('file', $test['file']);
        $testcase->setAttribute('line', $test['line']);
        $testcase->setAttribute('assertions', 1);
        if (!$test['status'])
        {
          $testcase->appendChild($failure = $dom->createElement('failure'));
          $failure->setAttribute('type', 'lime');
          if (isset($test['error']))
          {
            $failure->appendChild($dom->createTextNode($test['error']));
          }
        }
      }
    }

    $testsuites->setAttribute('failures', $failures);
    $testsuites->setAttribute('errors', $errors);
    $testsuites->setAttribute('tests', $assertions);
    $testsuites->setAttribute('assertions', $assertions);
    $testsuites->setAttribute('skipped', $skipped);

    return $dom->saveXml();
  }

  public function __destruct()
  {
    $plan = $this->results['stats']['plan'];
    $passed = count($this->results['stats']['passed']);
    $failed = count($this->results['stats']['failed']);
    $total = $this->results['stats']['total'];
    is_null($plan) and $plan = $total and $this->output->echoln(sprintf("1..%d", $plan));

    if ($total > $plan)
    {
      $this->output->red_bar(sprintf("# Looks like you planned %d tests but ran %d extra.", $plan, $total - $plan));
    }
    elseif ($total < $plan)
    {
      $this->output->red_bar(sprintf("# Looks like you planned %d tests but only ran %d.", $plan, $total));
    }

    if ($failed)
    {
      $this->output->red_bar(sprintf("# Looks like you failed %d tests of %d.", $failed, $passed + $failed));
    }
    else if ($total == $plan)
    {
      $this->output->green_bar("# Looks like everything went fine.");
    }

    flush();
  }

  /**
   * Tests a condition and passes if it is true
   *
   * @param mixed  $exp     condition to test
   * @param string $message display output message when the test passes
   *
   * @return boolean
   */
  public function ok($exp, $message = '')
  {
    $this->update_stats();

    if ($result = (boolean) $exp)
    {
      $this->results['stats']['passed'][] = $this->test_nb;
    }
    else
    {
      $this->results['stats']['failed'][] = $this->test_nb;
    }
    $this->results['tests'][$this->test_nb]['message'] = $message;
    $this->results['tests'][$this->test_nb]['status'] = $result;
    $this->output->echoln(sprintf("%s %d%s", $result ? 'ok' : 'not ok', $this->test_nb, $message = $message ? sprintf('%s %s', 0 === strpos($message, '#') ? '' : ' -', $message) : ''));

    if (!$result)
    {
      $this->output->diag(sprintf('    Failed test (%s at line %d)', str_replace(getcwd(), '.', $this->results['tests'][$this->test_nb]['file']), $this->results['tests'][$this->test_nb]['line']));
    }

    return $result;
  }

  /**
   * Compares two values and passes if they are equal (==)
   *
   * @param mixed  $exp1    left value
   * @param mixed  $exp2    right value
   * @param string $message display output message when the test passes
   *
   * @return boolean
   */
  public function is($exp1, $exp2, $message = '')
  {
    if (is_object($exp1) || is_object($exp2))
    {
      $value = $exp1 === $exp2;
    }
    else if (is_float($exp1) && is_float($exp2))
    {
      $value = abs($exp1 - $exp2) < self::EPSILON;
    }
    else
    {
      $value = $exp1 == $exp2;
    }

    if (!$result = $this->ok($value, $message))
    {
      $this->set_last_test_errors(array(sprintf("           got: %s", var_export($exp1, true)), sprintf("      expected: %s", var_export($exp2, true))));
    }

    return $result;
  }

  /**
   * Compares two values and passes if they are not equal
   *
   * @param mixed  $exp1    left value
   * @param mixed  $exp2    right value
   * @param string $message display output message when the test passes
   *
   * @return boolean
   */
  public function isnt($exp1, $exp2, $message = '')
  {
    if (!$result = $this->ok($exp1 != $exp2, $message))
    {
      $this->set_last_test_errors(array(sprintf("      %s", var_export($exp2, true)), '          ne', sprintf("      %s", var_export($exp2, true))));
    }

    return $result;
  }

  /**
   * Tests a string against a regular expression
   *
   * @param string $exp     value to test
   * @param string $regex   the pattern to search for, as a string
   * @param string $message display output message when the test passes
   *
   * @return boolean
   */
  public function like($exp, $regex, $message = '')
  {
    if (!$result = $this->ok(preg_match($regex, $exp), $message))
    {
      $this->set_last_test_errors(array(sprintf("                    '%s'", $exp), sprintf("      doesn't match '%s'", $regex)));
    }

    return $result;
  }

  /**
   * Checks that a string doesn't match a regular expression
   *
   * @param string $exp     value to test
   * @param string $regex   the pattern to search for, as a string
   * @param string $message display output message when the test passes
   *
   * @return boolean
   */
  public function unlike($exp, $regex, $message = '')
  {
    if (!$result = $this->ok(!preg_match($regex, $exp), $message))
    {
      $this->set_last_test_errors(array(sprintf("               '%s'", $exp), sprintf("      matches '%s'", $regex)));
    }

    return $result;
  }

  /**
   * Compares two arguments with an operator
   *
   * @param mixed  $exp1    left value
   * @param string $op      operator
   * @param mixed  $exp2    right value
   * @param string $message display output message when the test passes
   *
   * @return boolean
   */
  public function cmp_ok($exp1, $op, $exp2, $message = '')
  {
    $php = sprintf("\$result = \$exp1 $op \$exp2;");
    // under some unknown conditions the sprintf() call causes a segmentation fault
    // when placed directly in the eval() call
    eval($php);

    if (!$this->ok($result, $message))
    {
      $this->set_last_test_errors(array(sprintf("      %s", str_replace("\n", '', var_export($exp1, true))), sprintf("          %s", $op), sprintf("      %s", str_replace("\n", '', var_export($exp2, true)))));
    }

    return $result;
  }

  /**
   * Checks the availability of a method for an object or a class
   *
   * @param mixed        $object  an object instance or a class name
   * @param string|array $methods one or more method names
   * @param string       $message display output message when the test passes
   *
   * @return boolean
   */
  public function can_ok($object, $methods, $message = '')
  {
    $result = true;
    $failed_messages = array();
    foreach ((array) $methods as $method)
    {
      if (!method_exists($object, $method))
      {
        $failed_messages[] = sprintf("      method '%s' does not exist", $method);
        $result = false;
      }
    }

    !$this->ok($result, $message);

    !$result and $this->set_last_test_errors($failed_messages);

    return $result;
  }

  /**
   * Checks the type of an argument
   *
   * @param mixed  $var     variable instance
   * @param string $class   class or type name
   * @param string $message display output message when the test passes
   *
   * @return boolean
   */
  public function isa_ok($var, $class, $message = '')
  {
    $type = is_object($var) ? get_class($var) : gettype($var);
    if (!$result = $this->ok($type == $class, $message))
    {
      $this->set_last_test_errors(array(sprintf("      variable isn't a '%s' it's a '%s'", $class, $type)));
    }

    return $result;
  }

  /**
   * Checks that two arrays have the same values
   *
   * @param mixed  $exp1    first variable
   * @param mixed  $exp2    second variable
   * @param string $message display output message when the test passes
   *
   * @return boolean
   */
  public function is_deeply($exp1, $exp2, $message = '')
  {
    if (!$result = $this->ok($this->test_is_deeply($exp1, $exp2), $message))
    {
      $this->set_last_test_errors(array(sprintf("           got: %s", str_replace("\n", '', var_export($exp1, true))), sprintf("      expected: %s", str_replace("\n", '', var_export($exp2, true)))));
    }

    return $result;
  }

  /**
   * Always passes--useful for testing exceptions
   *
   * @param string $message display output message
   *
   * @return true
   */
  public function pass($message = '')
  {
    return $this->ok(true, $message);
  }

  /**
   * Always fails--useful for testing exceptions
   *
   * @param string $message display output message
   *
   * @return false
   */
  public function fail($message = '')
  {
    return $this->ok(false, $message);
  }

  /**
   * Outputs a diag message but runs no test
   *
   * @param string $message display output message
   *
   * @return void
   */
  public function diag($message)
  {
    $this->output->diag($message);
  }

  /**
   * Counts as $nb_tests tests--useful for conditional tests
   *
   * @param string  $message  display output message
   * @param integer $nb_tests number of tests to skip
   *
   * @return void
   */
  public function skip($message = '', $nb_tests = 1)
  {
    for ($i = 0; $i < $nb_tests; $i++)
    {
      $this->pass(sprintf("# SKIP%s", $message ? ' '.$message : ''));
      $this->results['stats']['skipped'][] = $this->test_nb;
      array_pop($this->results['stats']['passed']);
    }
  }

  /**
   * Counts as a test--useful for tests yet to be written
   *
   * @param string $message display output message
   *
   * @return void
   */
  public function todo($message = '')
  {
    $this->pass(sprintf("# TODO%s", $message ? ' '.$message : ''));
    $this->results['stats']['skipped'][] = $this->test_nb;
    array_pop($this->results['stats']['passed']);
  }

  /**
   * Validates that a file exists and that it is properly included
   *
   * @param string $file    file path
   * @param string $message display output message when the test passes
   *
   * @return boolean
   */
  public function include_ok($file, $message = '')
  {
    if (!$result = $this->ok((@include($file)) == 1, $message))
    {
      $this->set_last_test_errors(array(sprintf("      Tried to include '%s'", $file)));
    }

    return $result;
  }

  private function test_is_deeply($var1, $var2)
  {
    if (gettype($var1) != gettype($var2))
    {
      return false;
    }

    if (is_array($var1))
    {
      ksort($var1);
      ksort($var2);

      $keys1 = array_keys($var1);
      $keys2 = array_keys($var2);
      if (array_diff($keys1, $keys2) || array_diff($keys2, $keys1))
      {
        return false;
      }
      $is_equal = true;
      foreach ($var1 as $key => $value)
      {
        $is_equal = $this->test_is_deeply($var1[$key], $var2[$key]);
        if ($is_equal === false)
        {
          break;
        }
      }

      return $is_equal;
    }
    else
    {
      return $var1 === $var2;
    }
  }

  public function comment($message)
  {
    $this->output->comment($message);
  }

  public function info($message)
  {
    $this->output->info($message);
  }

  public function error($message, $file = null, $line = null, array $traces = array())
  {
    $this->output->error($message, $file, $line, $traces);

  	$this->results['stats']['errors'][] = array(
  	  'message' => $message,
  	  'file' => $file,
  	  'line' => $line,
  	);
  }

  protected function update_stats()
  {
    ++$this->test_nb;
    ++$this->results['stats']['total'];

    list($this->results['tests'][$this->test_nb]['file'], $this->results['tests'][$this->test_nb]['line']) = $this->find_caller(debug_backtrace());
  }

  protected function set_last_test_errors(array $errors)
  {
    $this->output->diag($errors);

    $this->results['tests'][$this->test_nb]['error'] = implode("\n", $errors);
  }

  protected function find_caller($traces)
  {
    // find the first call to a method of an object that is an instance of lime_test
    $t = array_reverse($traces);
    foreach ($t as $trace)
    {
      if (isset($trace['object']) && $trace['object'] instanceof lime_test)
      {
        return array($trace['file'], $trace['line']);
      }
    }

    // return the first call
    $last = count($traces) - 1;
    return array($traces[$last]['file'], $traces[$last]['line']);
  }

  public function handle_error($code, $message, $file, $line, $context)
  {
    if (!$this->options['error_reporting'] || ($code & error_reporting()) == 0)
    {
      return false;
    }

    switch ($code)
    {
      case E_WARNING:
        $type = 'Warning';
        break;
      default:
        $type = 'Notice';
        break;
    }

    $trace = debug_backtrace();
    array_shift($trace); // remove the handle_error() call from the trace

    $this->error($type.': '.$message, $file, $line, $trace);
  }

  public function handle_exception(Exception $exception)
  {
    $this->error(get_class($exception).': '.$exception->getMessage(), $exception->getFile(), $exception->getLine(), $exception->getTrace());

    // exception was handled
    return true;
  }
}

class lime_output
{
  public $colorizer = null;
  public $base_dir = null;

  public function __construct($force_colors = false, $base_dir = null)
  {
    $this->colorizer = new lime_colorizer($force_colors);
    $this->base_dir = $base_dir === null ? getcwd() : $base_dir;
  }

  public function diag()
  {
    $messages = func_get_args();
    foreach ($messages as $message)
    {
      echo $this->colorizer->colorize('# '.join("\n# ", (array) $message), 'COMMENT')."\n";
    }
  }

  public function comment($message)
  {
    echo $this->colorizer->colorize(sprintf('# %s', $message), 'COMMENT')."\n";
  }

  public function info($message)
  {
    echo $this->colorizer->colorize(sprintf('> %s', $message), 'INFO_BAR')."\n";
  }

  public function error($message, $file = null, $line = null, $traces = array())
  {
    if ($file !== null)
    {
      $message .= sprintf("\n(in %s on line %s)", $file, $line);
    }

    // some error messages contain absolute file paths
    $message = $this->strip_base_dir($message);

    $space = $this->colorizer->colorize(str_repeat(' ', 71), 'RED_BAR')."\n";
    $message = trim($message);
    $message = wordwrap($message, 66, "\n");

    echo "\n".$space;
    foreach (explode("\n", $message) as $message_line)
    {
      echo $this->colorizer->colorize(str_pad('  '.$message_line, 71, ' '), 'RED_BAR')."\n";
    }
    echo $space."\n";

    if (count($traces) > 0)
    {
      echo $this->colorizer->colorize('Exception trace:', 'COMMENT')."\n";

      $this->print_trace(null, $file, $line);

      foreach ($traces as $trace)
      {
        if (array_key_exists('class', $trace))
        {
          $method = sprintf('%s%s%s()', $trace['class'], $trace['type'], $trace['function']);
        }
        else
        {
          $method = sprintf('%s()', $trace['function']);
        }

        if (array_key_exists('file', $trace))
        {
          $this->print_trace($method, $trace['file'], $trace['line']);
        }
        else
        {
          $this->print_trace($method);
        }
      }

      echo "\n";
    }
  }

  protected function print_trace($method = null, $file = null, $line = null)
  {
    if (!is_null($method))
    {
      $method .= ' ';
    }

    echo '  '.$method.'at ';

    if (!is_null($file) && !is_null($line))
    {
      printf("%s:%s\n", $this->colorizer->colorize($this->strip_base_dir($file), 'TRACE'), $this->colorizer->colorize($line, 'TRACE'));
    }
    else
    {
      echo "[internal function]\n";
    }
  }

  public function echoln($message, $colorizer_parameter = null, $colorize = true)
  {
    if ($colorize)
    {
      $message = preg_replace('/(?:^|\.)((?:not ok|dubious|errors) *\d*)\b/e', '$this->colorizer->colorize(\'$1\', \'ERROR\')', $message);
      $message = preg_replace('/(?:^|\.)(ok *\d*)\b/e', '$this->colorizer->colorize(\'$1\', \'INFO\')', $message);
      $message = preg_replace('/"(.+?)"/e', '$this->colorizer->colorize(\'$1\', \'PARAMETER\')', $message);
      $message = preg_replace('/(\->|\:\:)?([a-zA-Z0-9_]+?)\(\)/e', '$this->colorizer->colorize(\'$1$2()\', \'PARAMETER\')', $message);
    }

    echo ($colorizer_parameter ? $this->colorizer->colorize($message, $colorizer_parameter) : $message)."\n";
  }

  public function green_bar($message)
  {
    echo $this->colorizer->colorize($message.str_repeat(' ', 71 - min(71, strlen($message))), 'GREEN_BAR')."\n";
  }

  public function red_bar($message)
  {
    echo $this->colorizer->colorize($message.str_repeat(' ', 71 - min(71, strlen($message))), 'RED_BAR')."\n";
  }

  protected function strip_base_dir($text)
  {
    return str_replace(DIRECTORY_SEPARATOR, '/', str_replace(realpath($this->base_dir).DIRECTORY_SEPARATOR, '', $text));
  }
}

class lime_output_color extends lime_output
{
}

class lime_colorizer
{
  static public $styles = array();

  protected $colors_supported = false;

  public function __construct($force_colors = false)
  {
    if ($force_colors)
    {
      $this->colors_supported = true;
    }
    else
    {
      // colors are supported on windows with ansicon or on tty consoles
      if (DIRECTORY_SEPARATOR == '\\')
      {
        $this->colors_supported = false !== getenv('ANSICON');
      }
      else
      {
        $this->colors_supported = function_exists('posix_isatty') && @posix_isatty(STDOUT);
      }
    }
  }

  public static function style($name, $options = array())
  {
    self::$styles[$name] = $options;
  }

  public function colorize($text = '', $parameters = array())
  {

    if (!$this->colors_supported)
    {
      return $text;
    }

    static $options    = array('bold' => 1, 'underscore' => 4, 'blink' => 5, 'reverse' => 7, 'conceal' => 8);
    static $foreground = array('black' => 30, 'red' => 31, 'green' => 32, 'yellow' => 33, 'blue' => 34, 'magenta' => 35, 'cyan' => 36, 'white' => 37);
    static $background = array('black' => 40, 'red' => 41, 'green' => 42, 'yellow' => 43, 'blue' => 44, 'magenta' => 45, 'cyan' => 46, 'white' => 47);

    !is_array($parameters) && isset(self::$styles[$parameters]) and $parameters = self::$styles[$parameters];

    $codes = array();
    isset($parameters['fg']) and $codes[] = $foreground[$parameters['fg']];
    isset($parameters['bg']) and $codes[] = $background[$parameters['bg']];
    foreach ($options as $option => $value)
    {
      isset($parameters[$option]) && $parameters[$option] and $codes[] = $value;
    }

    return "\033[".implode(';', $codes).'m'.$text."\033[0m";
  }
}

lime_colorizer::style('ERROR', array('bg' => 'red', 'fg' => 'white', 'bold' => true));
lime_colorizer::style('INFO', array('fg' => 'green', 'bold' => true));
lime_colorizer::style('TRACE', array('fg' => 'green', 'bold' => true));
lime_colorizer::style('PARAMETER', array('fg' => 'cyan'));
lime_colorizer::style('COMMENT', array('fg' => 'yellow'));

lime_colorizer::style('GREEN_BAR', array('fg' => 'white', 'bg' => 'green', 'bold' => true));
lime_colorizer::style('RED_BAR', array('fg' => 'white', 'bg' => 'red', 'bold' => true));
lime_colorizer::style('INFO_BAR', array('fg' => 'cyan', 'bold' => true));

class lime_harness extends lime_registration
{
  public $options = array();
  public $php_cli = null;
  public $stats   = array();
  public $output  = null;

  public function __construct($options = array())
  {
    // for BC
    if (!is_array($options))
    {
      $options = array('output' => $options);
    }

    $this->options = array_merge(array(
      'php_cli'      => null,
      'force_colors' => false,
      'output'       => null,
      'verbose'      => false,
    ), $options);

    $this->php_cli = $this->find_php_cli($this->options['php_cli']);
    $this->output = $this->options['output'] ? $this->options['output'] : new lime_output($this->options['force_colors']);
  }

  protected function find_php_cli($php_cli = null)
  {
    if (is_null($php_cli))
    {
      if (getenv('PHP_PATH'))
      {
        $php_cli = getenv('PHP_PATH');

        if (!is_executable($php_cli))
        {
          throw new Exception('The defined PHP_PATH environment variable is not a valid PHP executable.');
        }
      }
      else
      {
        $php_cli = PHP_BINDIR.DIRECTORY_SEPARATOR.'php';
      }
    }

    if (is_executable($php_cli))
    {
      return $php_cli;
    }

    $path = getenv('PATH') ? getenv('PATH') : getenv('Path');
    $exe_suffixes = DIRECTORY_SEPARATOR == '\\' ? (getenv('PATHEXT') ? explode(PATH_SEPARATOR, getenv('PATHEXT')) : array('.exe', '.bat', '.cmd', '.com')) : array('');
    foreach (array('php5', 'php') as $php_cli)
    {
      foreach ($exe_suffixes as $suffix)
      {
        foreach (explode(PATH_SEPARATOR, $path) as $dir)
        {
          $file = $dir.DIRECTORY_SEPARATOR.$php_cli.$suffix;
          if (is_executable($file))
          {
            return $file;
          }
        }
      }
    }

    throw new Exception("Unable to find PHP executable.");
  }

  public function to_array()
  {
    $results = array();
    foreach ($this->stats['files'] as $file => $stat)
    {
      $results = array_merge($results, $stat['output']);
    }

    return $results;
  }

  public function to_xml()
  {
    return lime_test::to_xml($this->to_array());
  }

  public function run()
  {
    if (!count($this->files))
    {
      throw new Exception('You must register some test files before running them!');
    }

    // sort the files to be able to predict the order
    sort($this->files);

    $this->stats = array(
      'files'        => array(),
      'failed_files' => array(),
      'failed_tests' => 0,
      'total'        => 0,
    );

    foreach ($this->files as $file)
    {
      $this->stats['files'][$file] = array();
      $stats = &$this->stats['files'][$file];

      $relative_file = $this->get_relative_file($file);

      $test_file = tempnam(sys_get_temp_dir(), 'lime');
      $result_file = tempnam(sys_get_temp_dir(), 'lime');
      file_put_contents($test_file, <<<EOF
<?php
function lime_shutdown()
{
  file_put_contents('$result_file', serialize(lime_test::to_array()));
}
register_shutdown_function('lime_shutdown');
include('$file');
EOF
      );

      ob_start();
      // see http://trac.symfony-project.org/ticket/5437 for the explanation on the weird "cd" thing
      passthru(sprintf('cd & %s %s 2>&1', escapeshellarg($this->php_cli), escapeshellarg($test_file)), $return);
      ob_end_clean();
      unlink($test_file);

      $output = file_get_contents($result_file);
      $stats['output'] = $output ? unserialize($output) : '';
      if (!$stats['output'])
      {
        $stats['output'] = array(array('file' => $file, 'tests' => array(), 'stats' => array('plan' => 1, 'total' => 1, 'failed' => array(0), 'passed' => array(), 'skipped' => array(), 'errors' => array())));
      }
      unlink($result_file);

      $file_stats = &$stats['output'][0]['stats'];

      $delta = 0;
      if ($return > 0)
      {
        $stats['status'] = $file_stats['errors'] ? 'errors' : 'dubious';
        $stats['status_code'] = $return;
      }
      else
      {
        $this->stats['total'] += $file_stats['total'];

        if (!$file_stats['plan'])
        {
          $file_stats['plan'] = $file_stats['total'];
        }

        $delta = $file_stats['plan'] - $file_stats['total'];
        if (0 != $delta)
        {
          $stats['status'] = $file_stats['errors'] ? 'errors' : 'dubious';
          $stats['status_code'] = 255;
        }
        else
        {
          $stats['status'] = $file_stats['failed'] ? 'not ok' : ($file_stats['errors'] ? 'errors' : 'ok');
          $stats['status_code'] = 0;
        }
      }

      $this->output->echoln(sprintf('%s%s%s', substr($relative_file, -min(67, strlen($relative_file))), str_repeat('.', 70 - min(67, strlen($relative_file))), $stats['status']));

      if ('dubious' == $stats['status'])
      {
        $this->output->echoln(sprintf('    Test returned status %s', $stats['status_code']));
      }

      if ('ok' != $stats['status'])
      {
        $this->stats['failed_files'][] = $file;
      }

      if ($delta > 0)
      {
        $this->output->echoln(sprintf('    Looks like you planned %d tests but only ran %d.', $file_stats['plan'], $file_stats['total']));

        $this->stats['failed_tests'] += $delta;
        $this->stats['total'] += $delta;
      }
      else if ($delta < 0)
      {
        $this->output->echoln(sprintf('    Looks like you planned %s test but ran %s extra.', $file_stats['plan'], $file_stats['total'] - $file_stats['plan']));
      }

      if (false !== $file_stats && $file_stats['failed'])
      {
        $this->stats['failed_tests'] += count($file_stats['failed']);

        $this->output->echoln(sprintf("    Failed tests: %s", implode(', ', $file_stats['failed'])));
      }

      if (false !== $file_stats && $file_stats['errors'])
      {
        $this->output->echoln('    Errors:');

        $error_count = count($file_stats['errors']);
        for ($i = 0; $i < 3 && $i < $error_count; ++$i)
        {
          $this->output->echoln('    - ' . $file_stats['errors'][$i]['message'], null, false);
        }
        if ($error_count > 3)
        {
          $this->output->echoln(sprintf('    ... and %s more', $error_count-3));
        }
      }
    }

    if (count($this->stats['failed_files']))
    {
      $format = "%-30s  %4s  %5s  %5s  %5s  %s";
      $this->output->echoln(sprintf($format, 'Failed Test', 'Stat', 'Total', 'Fail', 'Errors', 'List of Failed'));
      $this->output->echoln("--------------------------------------------------------------------------");
      foreach ($this->stats['files'] as $file => $stat)
      {
        if (!in_array($file, $this->stats['failed_files']))
        {
          continue;
        }
        $relative_file = $this->get_relative_file($file);

        if (isset($stat['output'][0]))
        {
          $this->output->echoln(sprintf($format, substr($relative_file, -min(30, strlen($relative_file))), $stat['status_code'], count($stat['output'][0]['stats']['failed']) + count($stat['output'][0]['stats']['passed']), count($stat['output'][0]['stats']['failed']), count($stat['output'][0]['stats']['errors']), implode(' ', $stat['output'][0]['stats']['failed'])));
        }
        else
        {
          $this->output->echoln(sprintf($format, substr($relative_file, -min(30, strlen($relative_file))), $stat['status_code'], '', '', ''));
        }
      }

      $this->output->red_bar(sprintf('Failed %d/%d test scripts, %.2f%% okay. %d/%d subtests failed, %.2f%% okay.',
        $nb_failed_files = count($this->stats['failed_files']),
        $nb_files = count($this->files),
        ($nb_files - $nb_failed_files) * 100 / $nb_files,
        $nb_failed_tests = $this->stats['failed_tests'],
        $nb_tests = $this->stats['total'],
        $nb_tests > 0 ? ($nb_tests - $nb_failed_tests) * 100 / $nb_tests : 0
      ));

      if ($this->options['verbose'])
      {
        foreach ($this->to_array() as $testsuite)
        {
          $first = true;
          foreach ($testsuite['stats']['failed'] as $testcase)
          {
            if (!isset($testsuite['tests'][$testcase]['file']))
            {
              continue;
            }

            if ($first)
            {
              $this->output->echoln('');
              $this->output->error($this->get_relative_file($testsuite['file']).$this->extension);
              $first = false;
            }

            $this->output->comment(sprintf('  at %s line %s', $this->get_relative_file($testsuite['tests'][$testcase]['file']).$this->extension, $testsuite['tests'][$testcase]['line']));
            $this->output->info('  '.$testsuite['tests'][$testcase]['message']);
            $this->output->echoln($testsuite['tests'][$testcase]['error'], null, false);
          }
        }
      }
    }
    else
    {
      $this->output->green_bar(' All tests successful.');
      $this->output->green_bar(sprintf(' Files=%d, Tests=%d', count($this->files), $this->stats['total']));
    }

    return $this->stats['failed_files'] ? false : true;
  }

  public function get_failed_files()
  {
    return isset($this->stats['failed_files']) ? $this->stats['failed_files'] : array();
  }
}

class lime_coverage extends lime_registration
{
  public $files = array();
  public $extension = '.php';
  public $base_dir = '';
  public $harness = null;
  public $verbose = false;
  protected $coverage = array();

  public function __construct($harness)
  {
    $this->harness = $harness;

    if (!function_exists('xdebug_start_code_coverage'))
    {
      throw new Exception('You must install and enable xdebug before using lime coverage.');
    }

    if (!ini_get('xdebug.extended_info'))
    {
      throw new Exception('You must set xdebug.extended_info to 1 in your php.ini to use lime coverage.');
    }
  }

  public function run()
  {
    if (!count($this->harness->files))
    {
      throw new Exception('You must register some test files before running coverage!');
    }

    if (!count($this->files))
    {
      throw new Exception('You must register some files to cover!');
    }

    $this->coverage = array();

    $this->process($this->harness->files);

    $this->output($this->files);
  }

  public function process($files)
  {
    if (!is_array($files))
    {
      $files = array($files);
    }

    $tmp_file = sys_get_temp_dir().DIRECTORY_SEPARATOR.'test.php';
    foreach ($files as $file)
    {
      $tmp = <<<EOF
<?php
xdebug_start_code_coverage(XDEBUG_CC_UNUSED | XDEBUG_CC_DEAD_CODE);
include('$file');
echo '<PHP_SER>'.serialize(xdebug_get_code_coverage()).'</PHP_SER>';
EOF;
      file_put_contents($tmp_file, $tmp);
      ob_start();
      // see http://trac.symfony-project.org/ticket/5437 for the explanation on the weird "cd" thing
      passthru(sprintf('cd & %s %s 2>&1', escapeshellarg($this->harness->php_cli), escapeshellarg($tmp_file)), $return);
      $retval = ob_get_clean();

      if (0 != $return) // test exited without success
      {
        // something may have gone wrong, we should warn the user so they know
        // it's a bug in their code and not symfony's

        $this->harness->output->echoln(sprintf('Warning: %s returned status %d, results may be inaccurate', $file, $return), 'ERROR');
      }

      if (false === $cov = @unserialize(substr($retval, strpos($retval, '<PHP_SER>') + 9, strpos($retval, '</PHP_SER>') - 9)))
      {
        if (0 == $return)
        {
          // failed to serialize, but PHP said it should of worked.
          // something is seriously wrong, so abort with exception
          throw new Exception(sprintf('Unable to unserialize coverage for file "%s"', $file));
        }
        else
        {
          // failed to serialize, but PHP warned us that this might have happened.
          // so we should ignore and move on
          continue; // continue foreach loop through $this->harness->files
        }
      }

      foreach ($cov as $file => $lines)
      {
        if (!isset($this->coverage[$file]))
        {
          $this->coverage[$file] = $lines;
          continue;
        }

        foreach ($lines as $line => $flag)
        {
          if ($flag == 1)
          {
            $this->coverage[$file][$line] = 1;
          }
        }
      }
    }

    if (file_exists($tmp_file))
    {
      unlink($tmp_file);
    }
  }

  public function output($files)
  {
    ksort($this->coverage);
    $total_php_lines = 0;
    $total_covered_lines = 0;
    foreach ($files as $file)
    {
      $file = realpath($file);
      $is_covered = isset($this->coverage[$file]);
      $cov = isset($this->coverage[$file]) ? $this->coverage[$file] : array();
      $covered_lines = array();
      $missing_lines = array();

      foreach ($cov as $line => $flag)
      {
        switch ($flag)
        {
          case 1:
            $covered_lines[] = $line;
            break;
          case -1:
            $missing_lines[] = $line;
            break;
        }
      }

      $total_lines = count($covered_lines) + count($missing_lines);
      if (!$total_lines)
      {
        // probably means that the file is not covered at all!
        $total_lines = count($this->get_php_lines(file_get_contents($file)));
      }

      $output = $this->harness->output;
      $percent = $total_lines ? count($covered_lines) * 100 / $total_lines : 0;

      $total_php_lines += $total_lines;
      $total_covered_lines += count($covered_lines);

      $relative_file = $this->get_relative_file($file);
      $output->echoln(sprintf("%-70s %3.0f%%", substr($relative_file, -min(70, strlen($relative_file))), $percent), $percent == 100 ? 'INFO' : ($percent > 90 ? 'PARAMETER' : ($percent < 20 ? 'ERROR' : '')));
      if ($this->verbose && $is_covered && $percent != 100)
      {
        $output->comment(sprintf("missing: %s", $this->format_range($missing_lines)));
      }
    }

    $output->echoln(sprintf("TOTAL COVERAGE: %3.0f%%", $total_php_lines ? $total_covered_lines * 100 / $total_php_lines : 0));
  }

  public static function get_php_lines($content)
  {
    if (is_readable($content))
    {
      $content = file_get_contents($content);
    }

    $tokens = token_get_all($content);
    $php_lines = array();
    $current_line = 1;
    $in_class = false;
    $in_function = false;
    $in_function_declaration = false;
    $end_of_current_expr = true;
    $open_braces = 0;
    foreach ($tokens as $token)
    {
      if (is_string($token))
      {
        switch ($token)
        {
          case '=':
            if (false === $in_class || (false !== $in_function && !$in_function_declaration))
            {
              $php_lines[$current_line] = true;
            }
            break;
          case '{':
            ++$open_braces;
            $in_function_declaration = false;
            break;
          case ';':
            $in_function_declaration = false;
            $end_of_current_expr = true;
            break;
          case '}':
            $end_of_current_expr = true;
            --$open_braces;
            if ($open_braces == $in_class)
            {
              $in_class = false;
            }
            if ($open_braces == $in_function)
            {
              $in_function = false;
            }
            break;
        }

        continue;
      }

      list($id, $text) = $token;

      switch ($id)
      {
        case T_CURLY_OPEN:
        case T_DOLLAR_OPEN_CURLY_BRACES:
          ++$open_braces;
          break;
        case T_WHITESPACE:
        case T_OPEN_TAG:
        case T_CLOSE_TAG:
          $end_of_current_expr = true;
          $current_line += count(explode("\n", $text)) - 1;
          break;
        case T_COMMENT:
        case T_DOC_COMMENT:
          $current_line += count(explode("\n", $text)) - 1;
          break;
        case T_CLASS:
          $in_class = $open_braces;
          break;
        case T_FUNCTION:
          $in_function = $open_braces;
          $in_function_declaration = true;
          break;
        case T_AND_EQUAL:
        case T_BREAK:
        case T_CASE:
        case T_CATCH:
        case T_CLONE:
        case T_CONCAT_EQUAL:
        case T_CONTINUE:
        case T_DEC:
        case T_DECLARE:
        case T_DEFAULT:
        case T_DIV_EQUAL:
        case T_DO:
        case T_ECHO:
        case T_ELSEIF:
        case T_EMPTY:
        case T_ENDDECLARE:
        case T_ENDFOR:
        case T_ENDFOREACH:
        case T_ENDIF:
        case T_ENDSWITCH:
        case T_ENDWHILE:
        case T_EVAL:
        case T_EXIT:
        case T_FOR:
        case T_FOREACH:
        case T_GLOBAL:
        case T_IF:
        case T_INC:
        case T_INCLUDE:
        case T_INCLUDE_ONCE:
        case T_INSTANCEOF:
        case T_ISSET:
        case T_IS_EQUAL:
        case T_IS_GREATER_OR_EQUAL:
        case T_IS_IDENTICAL:
        case T_IS_NOT_EQUAL:
        case T_IS_NOT_IDENTICAL:
        case T_IS_SMALLER_OR_EQUAL:
        case T_LIST:
        case T_LOGICAL_AND:
        case T_LOGICAL_OR:
        case T_LOGICAL_XOR:
        case T_MINUS_EQUAL:
        case T_MOD_EQUAL:
        case T_MUL_EQUAL:
        case T_NEW:
        case T_OBJECT_OPERATOR:
        case T_OR_EQUAL:
        case T_PLUS_EQUAL:
        case T_PRINT:
        case T_REQUIRE:
        case T_REQUIRE_ONCE:
        case T_RETURN:
        case T_SL:
        case T_SL_EQUAL:
        case T_SR:
        case T_SR_EQUAL:
        case T_SWITCH:
        case T_THROW:
        case T_TRY:
        case T_UNSET:
        case T_UNSET_CAST:
        case T_USE:
        case T_WHILE:
        case T_XOR_EQUAL:
          $php_lines[$current_line] = true;
          $end_of_current_expr = false;
          break;
        default:
          if (false === $end_of_current_expr)
          {
            $php_lines[$current_line] = true;
          }
      }
    }

    return $php_lines;
  }

  public function compute($content, $cov)
  {
    $php_lines = self::get_php_lines($content);

    // we remove from $cov non php lines
    foreach (array_diff_key($cov, $php_lines) as $line => $tmp)
    {
      unset($cov[$line]);
    }

    return array($cov, $php_lines);
  }

  public function format_range($lines)
  {
    sort($lines);
    $formatted = '';
    $first = -1;
    $last = -1;
    foreach ($lines as $line)
    {
      if ($last + 1 != $line)
      {
        if ($first != -1)
        {
          $formatted .= $first == $last ? "$first " : "[$first - $last] ";
        }
        $first = $line;
        $last = $line;
      }
      else
      {
        $last = $line;
      }
    }
    if ($first != -1)
    {
      $formatted .= $first == $last ? "$first " : "[$first - $last] ";
    }

    return $formatted;
  }
}

class lime_registration
{
  public $files = array();
  public $extension = '.php';
  public $base_dir = '';

  public function register($files_or_directories)
  {
    foreach ((array) $files_or_directories as $f_or_d)
    {
      if (is_file($f_or_d))
      {
        $this->files[] = realpath($f_or_d);
      }
      elseif (is_dir($f_or_d))
      {
        $this->register_dir($f_or_d);
      }
      else
      {
        throw new Exception(sprintf('The file or directory "%s" does not exist.', $f_or_d));
      }
    }
  }

  public function register_glob($glob)
  {
    if ($dirs = glob($glob))
    {
      foreach ($dirs as $file)
      {
        $this->files[] = realpath($file);
      }
    }
  }

  public function register_dir($directory)
  {
    if (!is_dir($directory))
    {
      throw new Exception(sprintf('The directory "%s" does not exist.', $directory));
    }

    $files = array();

    $current_dir = opendir($directory);
    while ($entry = readdir($current_dir))
    {
      if ($entry == '.' || $entry == '..') continue;

      if (is_dir($entry))
      {
        $this->register_dir($entry);
      }
      elseif (preg_match('#'.$this->extension.'$#', $entry))
      {
        $files[] = realpath($directory.DIRECTORY_SEPARATOR.$entry);
      }
    }

    $this->files = array_merge($this->files, $files);
  }

  protected function get_relative_file($file)
  {
    return str_replace(DIRECTORY_SEPARATOR, '/', str_replace(array(realpath($this->base_dir).DIRECTORY_SEPARATOR, $this->extension), '', $file));
  }
}
