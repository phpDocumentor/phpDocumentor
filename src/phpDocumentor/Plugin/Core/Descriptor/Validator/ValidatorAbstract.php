<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2013 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Plugin\Core\Descriptor\Validator;

use phpDocumentor\Reflection\BaseReflector;
use phpDocumentor\Reflection\DocBlock;

/**
 * Base class for DocBlock Validations.
 */
abstract class ValidatorAbstract
{
    /**
     * Name of the "entity" being validated.
     *
     * @var string
     */
    protected $entityName;

    /**
     * Line number of the docblock
     *
     * @var int
     */
    protected $lineNumber;

    /**
     * Docblock for the file.
     *
     * @var \phpDocumentor\Reflection\DocBlock
     */
    protected $docblock;

    /**
     * Source element of the DocBlock.
     *
     * @var \phpDocumentor\Reflection\BaseReflector
     */
    protected $source;

    /**
     * Array of options that may or may not be used whilst validating
     *
     * @var array
     */
    protected $options;

    /**
     * Constructor
     *
     * @param string             $name     Name of the "entity"
     * @param DocBlock|null      $docblock Docblock
     * @param BaseReflector|null $source   Source Element.
     */
    public function __construct(
        $name,
        $docblock = null,
        $source = null
    ) {
        $this->entityName = $name;
        $this->lineNumber = $docblock
            ? $docblock->getLocation()->getLineNumber()
            : $source->getLineNumber();
        $this->docblock   = $docblock;
        $this->source     = $source;
    }

    /**
     * Set the options that may be used whilst validating the docblocks.
     * Can contain configuration as long as each validator knows how to
     * interrogate it
     *
     * @param array $options Options that may be used during validation
     *
     * @return void
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }

    abstract public function isValid(BaseReflector $element);

    /**
     * Dispatches an event to the Event Dispatcher.
     *
     * This method tries to dispatch an event; if no Event Dispatcher has been
     * set than this method will explicitly not fail and return null.
     *
     * By not failing we make the Event Dispatcher optional and is it easier
     * for people to re-use this component in their own application.
     *
     * @param string        $name  Name of the event to dispatch.
     * @param EventAbstract $event Arguments for this event.
     *
     * @throws Exception if there is a dispatcher but it is not of type EventDispatcher
     *
     * @return void
     */
    public function dispatch($name, $event)
    {
        if (!$this->event_dispatcher) {
            return null;
        }

        if (!$this->event_dispatcher instanceof \phpDocumentor\Event\Dispatcher) {
            throw new Exception(
                'Expected the event dispatcher to be an instance of '
                    . 'phpDocumentor\Event\Dispatcher'
            );
        }

        $this->event_dispatcher->dispatch($name, $event);
    }

    /**
     * Dispatches a logging request.
     *
     * @param string $message  The message to log.
     * @param int    $priority The logging priority, the lower,
     *  the more important. Ranges from 1 to 7
     *
     * @return void
     */
    public function log($message, $priority = 6)
    {
        $this->dispatch(
            'system.log',
            \phpDocumentor\Event\LogEvent::createInstance($this)->setMessage($message)->setPriority($priority)
        );
    }

    /**
     * Dispatches a parser error to be logged.
     *
     * @param string   $type      The logging priority as string
     * @param string   $message   The message to log.
     * @param string   $line      The line number where the error occurred..
     * @param string[] $variables an array with message substitution variables.
     *
     * @return void
     */
    public function logParserError($type, $code, $line, $variables = array())
    {
        $message = $this->_($code, $variables);
        $this->log($message, \phpDocumentor\Plugin\Core\Log::ERR);
        $this->dispatch(
            'parser.log',
            \phpDocumentor\Parser\Event\LogEvent::createInstance($this)
                ->setMessage($message)->setType($type)->setCode($code)->setLine($line)
        );
    }

    /**
     * Dispatches a logging request to log a debug message.
     *
     * @param string $message The message to log.
     *
     * @return void
     */
    public function debug($message)
    {
        $this->dispatch('system.debug', \phpDocumentor\Event\DebugEvent::createInstance($this)->setMessage($message));
    }

    /**
     * Translates the ID or message in the given language.
     *
     * Translation messages may contain any formatting as used by the php
     * vsprintf function.
     *
     * @param string $message   ID or message to translate.
     * @param array  $variables Variables to use for substitution.
     *
     * @return string
     */
    public function _($message, $variables = array())
    {
        if (!$this->translate) {
            return vsprintf($message, $variables);
        }

        return vsprintf($this->translate->translate($message), $variables);
    }

    /**
     * Returns the configuration for this object.
     *
     * @return \Zend\Config\Config
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * Returns the event dispatcher.
     *
     * @return \phpDocumentor\Event\Dispatcher
     */
    public function getEventDispatcher()
    {
        return $this->event_dispatcher;
    }

    /**
     * Returns the translation component.
     *
     * @return Translator|null
     */
    public function getTranslator()
    {
        return $this->translate;
    }
}
