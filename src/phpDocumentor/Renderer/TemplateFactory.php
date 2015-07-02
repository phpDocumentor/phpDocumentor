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

namespace phpDocumentor\Renderer;

use Webmozart\Assert\Assert;
use phpDocumentor\Renderer\Template\Parameter;

/**
 * Creates a new Template based on an array with a Template definition.
 */
final class TemplateFactory
{
    /**
     * Creates a new Template entity with the given name, parameters and options.
     *
     * @param string[] $options Array with a 'name', 'parameters' and 'actions' key.
     *
     * @throws \InvalidArgumentException if the given options array does not map onto a Template.
     *
     * @return Template
     */
    public function create(array $options)
    {
        Assert::keyExists($options, 'name');
        Assert::stringNotEmpty($options['name']);
        $template = new Template($options['name']);

        if (!isset($options['parameters'])) {
            $options['parameters'] = [];
        }
        Assert::isArray($options['parameters']);
        $this->addParametersToTemplate($options['parameters'], $template);

        if (!isset($options['actions'])) {
            $options['actions'] = [];
        }
        Assert::isArray($options['actions']);
        $this->addActionsToTemplate($options['actions'], $template);

        return $template;
    }

    /**
     * Create Parameter value objects from the given parameters array and adds them to the Template.
     *
     * @param string[] $parameters Array containing a 'key' and 'value' key.
     * @param Template $template
     *
     * @throws \InvalidArgumentException if one of the parameters is not a valid array
     * @throws \InvalidArgumentException if one of the parameters does not contain the 'key' element
     * @throws \InvalidArgumentException if one of the parameters does not contain the 'value' element
     *
     * @return void
     */
    private function addParametersToTemplate(array $parameters, Template $template)
    {
        foreach ($parameters as $parameter) {
            Assert::isArray($parameter);
            Assert::keyExists($parameter, 'key');
            Assert::keyExists($parameter, 'value');
            $template->with(new Template\Parameter($parameter['key'], $parameter['value']));
        }
    }

    /**
     * Adds a series of Actions, as defined by entries in the $actions array, to the provided Template.
     *
     * @param string[] $actions
     * @param Template $template
     *
     * @throws \InvalidArgumentException if any element in the actions array is not an array.
     *
     * @return void
     */
    private function addActionsToTemplate($actions, Template $template)
    {
        foreach ($actions as $action) {
            Assert::isArray($action);
            $this->addActionToTemplate($template, $action);
        }
    }

    /**
     * Add a new Action to the Template which belongs to the given 'name' index.
     *
     * To create the new Action its 'create' factory method is used and the template and action parameters are provided
     * to it.
     *
     * @param Template $template
     * @param string[] $action
     *
     * @throws \InvalidArgumentException if the class deduced from the 'name' field does not exist
     * @throws \InvalidArgumentException if the class deduced from the 'name' field does not implement the Action
     *     interface
     * @throws \RuntimeException if the factory method of the deduced Action does not return an Action object.
     *
     * @return void
     */
    private function addActionToTemplate(Template $template, $action)
    {
        $actionClass = $action['name'];
        if (strpos($actionClass, '\\') === false) {
            $actionClass = 'phpDocumentor\\Renderer\\Action\\' . $actionClass;
        }

        Assert::classExists($actionClass);
        Assert::implementsInterface($actionClass, Action::class);
        $parameters = $this->mergeTemplateParametersWithActionParameters($template, $action);

        $actionObject = call_user_func([$actionClass, 'create'], $parameters);
        if (!$actionObject instanceof Action) {
            throw new \RuntimeException(
                'An error occurred when constructing an Action object; nothing was returned. This is most probably'
                . ' caused by a forgotten return statement in the "create" factory method of the class ' . $actionClass
            );
        }

        $template->handles($actionObject);
    }

    /**
     * Merges the Template's Parameters together with the array containing parameter entries into one array of
     * Parameter objects.
     *
     * Please note that when there are duplicate keys that the Action's parameter overwrites the Template's parameter
     * for this specific action.
     *
     * @param Template $template
     * @param string[]|string[][] $action
     *
     * @throws \InvalidArgumentException if one of the parameters is not a valid array
     * @throws \InvalidArgumentException if one of the parameters does not contain the 'key' element
     * @throws \InvalidArgumentException if one of the parameters does not contain the 'value' element
     *
     * @return Template\Parameter[]
     */
    private function mergeTemplateParametersWithActionParameters(Template $template, $action)
    {
        $parameters = $template->getParameters();
        if (!isset($action['parameters'])) {
            $action['parameters'] = [];
        }

        foreach ($action['parameters'] as $parameter) {
            Assert::isArray($parameter);
            Assert::keyExists($parameter, 'key');
            Assert::keyExists($parameter, 'value');
            $parameters[$parameter['key']] = new Parameter($parameter['key'], $parameter['value']);
        }

        return $parameters;
    }
}
