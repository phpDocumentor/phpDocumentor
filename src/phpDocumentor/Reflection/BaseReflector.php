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

namespace phpDocumentor\Reflection;

/**
 * Basic reflection providing support for events and basic properties as a
 * DocBlock and names.
 *
 * @author  Mike van Riel <mike.vanriel@naenius.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    http://phpdoc.org
 */
abstract class BaseReflector
{
    /** @var \PHPParser_Node_Stmt */
    protected $node;

    protected $default_package_name = '';

    /**
     * The event dispatcher object, may be null to not dispatch events.
     *
     * @var \sfEventDispatcher|null
     */
    public static $event_dispatcher = null;

    /**
     * Dispatches an event to the Event Dispatcher.
     *
     * This method tries to dispatch an event; if no Event Dispatcher has been
     * set than this method will explicitly not fail and return null.
     *
     * By not failing we make the Event Dispatcher optional and is it easier
     * for people to re-use this component in their own application.
     *
     * @param string   $name      Name of the event to dispatch.
     * @param string[] $arguments Arguments for this event.
     *
     * @throws \phpDocumentor\Reflection\Exception if there is a dispatcher but
     *  it is not of type sfEventDispatcher
     *
     * @return mixed|null
     */
    public function dispatch($name, $arguments)
    {
        if (!self::$event_dispatcher) {
            return null;
        }

        if (!self::$event_dispatcher instanceof \sfEventDispatcher) {
            throw new \phpDocumentor\Reflection\Exception(
                'Expected the event dispatcher to be an instance of '
                . 'sfEventDispatcher'
            );
        }

        $event = self::$event_dispatcher->notify(
            new \sfEvent($this, $name, $arguments)
        );

        return $event ? $event->getReturnValue() : null;
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
            array(
                 'message' => $message,
                 'priority' => $priority
            )
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
        $this->dispatch(
            'system.debug',
            array('message' => $message)
        );
    }

    /**
     * Initializes this reflector with the correct node as produced by
     * PHP-Parser.
     *
     * @link http://github.com/nikic/PHP-Parser
     *
     * @param \PHPParser_NodeAbstract $node
     */
    public function __construct(\PHPParser_NodeAbstract $node)
    {
        $this->node = $node;
    }

    /**
     * Returns the parsed DocBlock.
     *
     * @return \phpDocumentor\Reflection\DocBlock|null
     */
    public function getDocBlock()
    {
        $doc_block = null;
        if ((string)$this->node->getDocComment()) {
            $doc_block = new \phpDocumentor\Reflection\DocBlock(
                (string)$this->node->getDocComment()
            );
            $doc_block->line_number = $this->node->getLine();
        }

        $this->dispatch(
            'reflection.docblock-extraction.post',
            array('docblock' => $doc_block)
        );

        return $doc_block;
    }

    /**
     * Returns the name for this Reflector instance.
     *
     * @return string
     */
    public function getName()
    {
        if (isset($this->node->namespacedName)) {
            return '\\'.implode('\\', $this->node->namespacedName->parts);
        }

        return $this->getShortName();
    }

    /**
     * Returns the last component of a namespaced name as a short form.
     *
     * @return string
     */
    public function getShortName()
    {
        return isset($this->node->name)
            ? $this->node->name
            : (string)$this->node;
    }

    /**
     * Returns the namespace name for this object.
     *
     * If this object does not have a namespace then an empty string is
     * returned.
     *
     * @return string
     */
    public function getNamespace()
    {
        if (!$this->node->namespacedName) {
            return '';
        }

        $parts = $this->node->namespacedName->parts;
        array_pop($parts);

        return implode('\\', $parts);
    }

    /**
     * Returns the line number where this object starts.
     *
     * @return int
     */
    public function getLinenumber()
    {
        return $this->node->getLine();
    }

    /**
     * Sets the default package name for this object.
     *
     * If the DocBlock contains a different package name then that overrides
     * this package name.
     *
     * @param string $default_package_name The name of the package as defined
     *     in the PHPDoc Standard.
     *
     * @return void
     */
    public function setDefaultPackageName($default_package_name)
    {
        $this->default_package_name = $default_package_name;
    }

    /**
     * Returns the package name that is default for this element.
     *
     * This value may change after the DocBlock is interpreted. If that contains
     * a package tag then that tag overrides the Default package name.
     *
     * @return string
     */
    public function getDefaultPackageName()
    {
        return $this->default_package_name;
    }

    /**
     * Returns a simple human readable output for a value.
     *
     * @param \PHPParser_Node_Expr $value The value node as provided by
     *     PHP-Parser.
     *
     * @return string
     */
    protected function getRepresentationOfValue($value)
    {
        if (!$value) {
            return '';
        }

        if (!$value instanceof \PHPParser_Node_Expr_Array) {
            return var_export($value->value, true);
        }

        $items = array();
        foreach ($value->items as $item) {
            $items[] = $this->getRepresentationOfValue($item->key) . ' => '
                . $this->getRepresentationOfValue($item->value);
        }

        return 'array(' . implode(', ', $items) . ')';
    }

    public function getNamespaceAliases()
    {
        return array();
    }
}
