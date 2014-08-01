<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Plugin\Scrybe\Template;

/**
 * A factory used to retrieve a template engine given a simplified name.
 *
 * With this factory it is possible to abstract away the actual class names and provide a faux name that is suitable
 * for configuration purposes. An additional benefit is that any plugin is able to register their own template engines
 * if desired.
 */
class Factory
{
    /** @var string[] Associative array with engine names as key and class names as value. */
    protected $engines = array();

    /**
     * Registers the default and provided Template engines.
     *
     * @param TemplateInterface[] $engines Associative array of the engine class names and their name as key.
     */
    public function __construct(array $engines = array())
    {
        foreach ($engines as $name => $class) {
            $this->register($name, $class);
        }
    }

    /**
     * Associates a human-readable / simplified name with a class name representing a template engine.
     *
     * The class belonging to the given class name should implement the TemplateInterface. If it does not then
     * this method won't complain (as no instantiation is done here for performance reasons) but the `get()` method
     * will throw an exception.
     *
     * @param string            $name
     * @param TemplateInterface $templateEngine
     *
     * @see get() to retrieve an instance for the given $name.
     *
     * @return void
     */
    public function register($name, TemplateInterface $templateEngine)
    {
        $this->engines[$name] = $templateEngine;
    }

    /**
     * Returns a new instance of the template engine belonging to the given name.
     *
     * @param string $name
     *
     * @throws \InvalidArgumentException if the given name is not registered
     *
     * @return TemplateInterface
     */
    public function get($name)
    {
        if (!isset($this->engines[$name])) {
            throw new \InvalidArgumentException('Template engine "'.$name.'" is not known or registered');
        }

        return $this->engines[$name];
    }
}
