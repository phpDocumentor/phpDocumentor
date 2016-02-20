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

namespace phpDocumentor\Infrastructure\Renderer;

use phpDocumentor\DomainModel\Renderer\Template;
use phpDocumentor\DomainModel\Renderer\TemplateFactory;
use phpDocumentor\DomainModel\Uri;
use phpDocumentor\DomainModel\Renderer\Template\Action;
use phpDocumentor\DomainModel\Renderer\RenderContext;
use Webmozart\Assert\Assert;
use phpDocumentor\DomainModel\Renderer\Template\Parameter;

/**
 * Creates a new Template based on an array with a Template definition.
 */
final class XmlTemplateFactory implements TemplateFactory
{
    private $templateFolders   = [];
    private $templateTemplates = [];

    public function __construct(array $templateFolders)
    {
        $this->templateFolders = $templateFolders;
    }

    /**
     * Creates a new Template entity with the given name, parameters and options.
     *
     * @param RenderContext $renderContext
     * @param string[] $options Array with a 'name', 'parameters' and 'actions' key.
     *
     * @throws \InvalidArgumentException if the given options array does not map onto a Template.
     *
     * @return Template
     */
    public function create(RenderContext $renderContext, array $options)
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
        $this->addActionsToTemplate($options['actions'], $renderContext, $template);

        return $template;
    }

    /**
     * @param RenderContext $renderContext
     * @param string     $name
     *
     * @return null|Template
     */
    public function createFromName(RenderContext $renderContext, $name)
    {
        Assert::stringNotEmpty($name);

        foreach ($this->templateFolders as $folder) {
            $path = rtrim($folder, '\\/') . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . 'template.xml';
            if (file_exists($path) && is_file($path)) {
                $template = $this->readFromXml($path);
                $template['name'] = $name;

                return $this->create($renderContext, $template);
            }
        }

        return null;
    }

    /**
     * @param RenderContext $renderContext
     * @param Uri        $uri
     *
     * @return Template
     */
    public function createFromUri(RenderContext $renderContext, Uri $uri)
    {
        $template = $this->readFromXml((string)$uri);

        return $this->create($renderContext, $template);
    }

    private function readFromXml($path)
    {
        if (isset($this->templateTemplates[$path])) {
            return $this->templateTemplates[$path];
        }

        libxml_use_internal_errors(false);
        $xml = simplexml_load_file($path);
        /** @var \LibXMLError[] $errors */
        $errors = libxml_get_errors();
        if (count($errors) > 0) {
            throw new \RuntimeException(sprintf('The template "%s" could not be loaded due to errors', $path));
        }

        $result = [
            'name'       => (string)$xml->name ?: basename(dirname($path)),
            'parameters' => [
                [ 'key' => 'directory', 'value' => dirname($path) ]
            ],
            'actions'    => [],
        ];

        if (isset($xml->parameters)) {
            /** @var \SimpleXMLElement $parameters */
            $parameters = $xml->parameters;

            /** @var \SimpleXMLElement $parameter */
            foreach ((array)$parameters->children() as $parameter) {
                $result[] = ['key' => $parameter->getName(), 'value' => (string)$parameter];
            }
        }

        // Backwards compatibility with phpDocumentor 2 template files
        if (isset($xml->transformations)) {
            /** @var \SimpleXMLElement $parameter */
            foreach ($xml->transformations->transformation as $transformation) {
                $name  = (string)$transformation['writer'];
                $query = (string)$transformation['query'];
                if (strtolower($name) === 'fileio') {
                    $name = ucfirst($query) . 'File';
                }
                $result['actions'][] = [
                    'name' => $name,
                    'parameters' => [
                        [ 'key' => 'query', 'value' => $query],
                        [ 'key' => 'source', 'value' => (string)$transformation['source'] ],
                        [ 'key' => 'destination', 'value' => (string)$transformation['artifact'] ],
                    ]
                ];
            }
        }

        if (isset($xml->action)) {
            /** @var \SimpleXMLElement $parameter */
            foreach ((array)$xml->action as $action) {
                $parameters = [];
                foreach ((array)$action->parameter as $parameter) {
                    $parameters[] = [ 'key' => $parameter['id'], 'value' => (string)$parameter ];
                }
                $result['actions'][] = [
                    'name'       => (string)$action['name'],
                    'parameters' => $parameters
                ];
            }
        }

        // cache template 'template'
        $this->templateTemplates[$path] = $result;

        return $result;
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
     * @param string[]   $actions
     * @param RenderContext $renderContext
     * @param Template   $template
     *
     * @return void
     */
    private function addActionsToTemplate($actions, RenderContext $renderContext, Template $template)
    {
        foreach ($actions as $action) {
            Assert::isArray($action);
            $this->addActionToTemplate($renderContext, $template, $action);
        }
    }

    /**
     * Add a new Action to the Template which belongs to the given 'name' index.
     *
     * To create the new Action its 'create' factory method is used and the template and action parameters are provided
     * to it.
     *
     * @param Template   $template
     * @param RenderContext $renderContext
     * @param string[]   $action
     *
     * @throws \InvalidArgumentException if the class deduced from the 'name' field does not exist
     * @throws \InvalidArgumentException if the class deduced from the 'name' field does not implement the Action
     *     interface
     * @throws \RuntimeException if the factory method of the deduced Action does not return an Action object.
     *
     * @return void
     */
    private function addActionToTemplate(RenderContext $renderContext, Template $template, $action)
    {
        $actionClass = $action['name'];
        if (strpos($actionClass, '\\') === false) {
            $actionClass = 'phpDocumentor\\Application\\Renderer\\Template\\Action\\' . $actionClass;
        }

        Assert::classExists($actionClass);
        Assert::implementsInterface($actionClass, Action::class);
        $parameters = $this->mergeTemplateParametersWithActionParameters($template, $action);
        $parameters['renderContext'] = new Parameter('renderContext', $renderContext);
        $parameters['template']   = new Parameter('template', $template);

        $actionObject = call_user_func([ $actionClass, 'create' ], $parameters);
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
