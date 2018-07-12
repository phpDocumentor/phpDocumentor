<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Plugin\Scrybe\Template;

use InvalidArgumentException;
use Webmozart\Assert\Assert;

/**
 * A factory used to retrieve a template engine given a simplified name.
 *
 * With this factory it is possible to abstract away the actual class names and provide a faux name that is suitable
 * for configuration purposes. An additional benefit is that any plugin is able to register their own template engines
 * if desired.
 */
class Factory
{
    /** @var TemplateInterface[] Associative array with engine names as key and class names as value. */
    protected $engines = [];

    /**
     * Registers the default and provided Template engines.
     *
     * @param TemplateInterface[] $engines Associative array of the engine class names and their name as key.
     */
    public function __construct(array $engines = [])
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
     * @see get() to retrieve an instance for the given $name.
     */
    public function register(string $name, TemplateInterface $templateEngine): void
    {
        Assert::stringNotEmpty($name);
        $this->engines[$name] = $templateEngine;
    }

    /**
     * Returns a new instance of the template engine belonging to the given name.
     *
     * @throws InvalidArgumentException if the given name is not registered
     */
    public function get(string $name): TemplateInterface
    {
        if (!isset($this->engines[$name])) {
            throw new InvalidArgumentException('Template engine "' . $name . '" is not known or registered');
        }

        return $this->engines[$name];
    }
}
