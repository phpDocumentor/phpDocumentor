<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2015 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\DomainModel;

use phpDocumentor\DomainModel\Template\Action;

/**
 * A Template is used to generate output based on the generated Documentation.
 *
 * In web applications you generally write a 'theme' that can be used by that application to
 * give a specific look and feel to it. With phpDocumentor this is somewhat different since
 * the Template also determines at which location to create pre-rendered output and has to copy
 * assets such as CSS files, Javascript and images.
 *
 * In order to do this you can define a series of Actions with a template that prescribe which
 * Actions to execute in the specified order.
 *
 * With a template you can provide a series of parameters that can be passed to every Action
 * so that a default piece of information is provided, such as the target location.
 */
final class Template
{
    /** @var string The name with which this template is identified */
    private $name;

    /** @var Template\Parameter[] A series of parameters that should be merged with the actions and act as defaults */
    private $parameters = [];

    /**
     * @var Action[] A series of commands/action definitions that determine how the renderer renders the documentation.
     */
    private $actions = [];

    /**
     * Initializes this entity with the given name and optionally a set of parameters and actions.
     *
     * @param string               $name
     * @param Template\Parameter[] $parameters
     * @param Action[]             $actions
     *
     * @throws \InvalidArgumentException if the name is not a string
     */
    public function __construct($name, array $parameters = array(), array $actions = array())
    {
        if (! is_string($name)) {
            throw new \InvalidArgumentException(
                'The name of a template should be a string, received: ' . var_export($name, true)
            );
        }
        $this->name = $name;

        foreach ($parameters as $parameter) {
            $this->with($parameter);
        }

        foreach ($actions as $action) {
            $this->handles($action);
        }
    }

    /**
     * Registers the given Parameter with this Template.
     *
     * @param Template\Parameter $parameter
     *
     * @return void
     */
    public function with(Template\Parameter $parameter)
    {
        $this->parameters[$parameter->getKey()] = $parameter;
    }

    /**
     * Registers an Action to be handled by this template once invoked by the Renderer.
     *
     * @param Action $action
     *
     * @return void
     */
    public function handles(Action $action)
    {
        $this->actions[] = $action;
    }

    /**
     * Returns the name for this template.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the parameters for this template.
     *
     * @return Template\Parameter[]
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Returns the actions that need to be executed for this template.
     *
     * @return Action[]
     */
    public function getActions()
    {
        return $this->actions;
    }
}
