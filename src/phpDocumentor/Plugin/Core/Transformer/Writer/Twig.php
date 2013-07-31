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

namespace phpDocumentor\Plugin\Core\Transformer\Writer;

use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Plugin\Core\Twig\Extension;
use phpDocumentor\Transformer\Router\ForFileProxy;
use phpDocumentor\Transformer\Router\Queue;
use phpDocumentor\Transformer\Template;
use phpDocumentor\Transformer\Transformation;
use phpDocumentor\Transformer\Writer\Routable;
use phpDocumentor\Transformer\Writer\WriterAbstract;
use phpDocumentor\Translator;

/**
 * A specialized writer which uses the Twig templating engine to convert
 * templates to HTML output.
 *
 * This writer support the Query attribute of a Transformation to generate
 * multiple templates in one transformation.
 *
 * The Query attribute supports a simplified version of Twig queries and will
 * use each individual result as the 'node' global variable in the Twig template.
 *
 * Example:
 *
 *   Suppose a Query `indexes.classes` is given then this writer will be
 *   invoked as many times as there are classes in the project and the
 *   'node' global variable in twig will be filled with each individual
 *   class entry.
 *
 * When using the Query attribute in the transformation it is important to
 * use a variable in the Artefact attribute as well (otherwise the same file
 * would be overwritten several times).
 *
 * A simple example transformation line could be:
 *
 *     ```
 *     <transformation
 *         writer="twig"
 *         source="templates/twig/index.twig"
 *         artifact="index.html"/>
 *     ```
 *
 *     This example transformation would use this writer to transform the
 *     index.twig template file in the twig template folder into index.html at
 *     the destination location.
 *     Since no Query is provided the 'node' global variable will contain
 *     the Project Descriptor of the Object Graph.
 *
 * A complex example transformation line could be:
 *
 *     ```
 *     <transformation
 *         query="indexes.classes"
 *         writer="twig"
 *         source="templates/twig/class.twig"
 *         artifact="{{name}}.html"/>
 *     ```
 *
 *     This example transformation would use this writer to transform the
 *     class.twig template file in the twig template folder into a file with
 *     the 'name' poperty for an individual class inside the Object Graph.
 *     Since a Query *is* provided will the 'node' global variable contain a
 *     specific instance of a class applicable to the current iteration.
 *
 * @see self::getDestinationPath() for more information about variables in the
 *     Artefact attribute.
 */
class Twig extends WriterAbstract implements Routable
{
    /** @var Queue $routers */
    protected $routers;

    /** @var Translator $translator */
    protected $translator;

    /**
     * This method combines the ProjectDescriptor and the given target template
     * and creates a static html page at the artifact location.
     *
     * @param ProjectDescriptor $project        Document containing the structure.
     * @param Transformation    $transformation Transformation to execute.
     *
     * @return void
     */
    public function transform(ProjectDescriptor $project, Transformation $transformation)
    {
        $template_path = $this->getTemplatePath($transformation);

        $nodes = $this->getListOfNodes($transformation->getQuery(), $project);

        foreach ($nodes as $node) {
            if (!$node) {
                continue;
            }
            $destination = $this->getDestinationPath($node, $transformation);
            if ($destination === false) {
                continue;
            }
            $environment = $this->initializeEnvironment($project, $transformation, $destination);
            $environment->addGlobal('node', $node);

            $html = $environment->render(substr($transformation->getSource(), strlen($template_path)));
            file_put_contents($destination, $html);
        }
    }

    /**
     * Combines the query and project to retrieve a list of nodes that are to be used as node-point in a template.
     *
     * This method interprets the provided query string and walks through the project descriptor to find the correct
     * element. This method will silently fail if an invalid query was provided; in such a case the project descriptor
     * is returned.
     *
     * @param string            $query
     * @param ProjectDescriptor $project
     *
     * @return \Traversable|mixed[]
     */
    protected function getListOfNodes($query, ProjectDescriptor $project)
    {
        if ($query) {
            $node = $this->walkObjectTree($project, $query);

            if (!is_array($node) && (!$node instanceof \Traversable)) {
                $node = array($node);
            }

            return $node;
        }

        return array($project);
    }

    /**
     * Walks an object graph and/or array using a twig query string.
     *
     * Note: this method is public because it is used in a closure in {{@see getDestinationPath()}}.
     *
     * @param \Traversable|mixed $objectOrArray
     * @param string             $query         A path to walk separated by dots, i.e. `namespace.namespaces`.
     *
     * @todo move this to a separate class and make it more flexible.
     *
     * @return mixed
     */
    public function walkObjectTree($objectOrArray, $query)
    {
        $node = $objectOrArray;
        $objectPath = explode('.', $query);

        // walk through the tree
        foreach ($objectPath as $pathNode) {
            if (is_array($node)) {
                if (isset($node[$pathNode])) {
                    $node = $node[$pathNode];
                    continue;
                }
            } elseif (is_object($node)) {
                if (isset($node->$pathNode) || (method_exists($node, '__get') && $node->$pathNode)) {
                    $node = $node->$pathNode;
                    continue;
                } elseif (method_exists($node, $pathNode)) {
                    $node = $node->$pathNode();
                    continue;
                } elseif (method_exists($node, 'get' . $pathNode)) {
                    $pathNode = 'get' . $pathNode;
                    $node = $node->$pathNode();
                    continue;
                } elseif (method_exists($node, 'is' . $pathNode)) {
                    $pathNode = 'is' . $pathNode;
                    $node = $node->$pathNode();
                    continue;
                }
            }

            return null;
        }

        return $node;
    }

    /**
     * Initializes the Twig environment with the template, base extension and additionally defined extensions.
     *
     * @param ProjectDescriptor $project
     * @param Transformation    $transformation
     * @param string            $destination
     *
     * @return \Twig_Environment
     */
    protected function initializeEnvironment(ProjectDescriptor $project, Transformation $transformation, $destination)
    {
        $callingTemplatePath = $this->getTemplatePath($transformation);

        $baseTemplatesPath = $transformation->getTransformer()->getTemplates()->getTemplatesPath();

        $templateFolders = array(
            $baseTemplatesPath . '/..' . DIRECTORY_SEPARATOR . $callingTemplatePath,
            // http://twig.sensiolabs.org/doc/recipes.html#overriding-a-template-that-also-extends-itself
            $baseTemplatesPath
        );

        // get all invoked template paths, they overrule the calling template path
        /** @var Template $template */
        foreach ($transformation->getTransformer()->getTemplates() as $template) {
            $path = $baseTemplatesPath . DIRECTORY_SEPARATOR . $template->getName();
            array_unshift($templateFolders, $path);
        }

        $env = new \Twig_Environment(new \Twig_Loader_Filesystem($templateFolders));

        $this->addPhpDocumentorExtension($project, $transformation, $destination, $env);
        $this->addExtensionsFromTemplateConfiguration($transformation, $project, $env);

        return $env;
    }

    /**
     * Adds the phpDocumentor base extension to the Twig Environment.
     *
     * @param ProjectDescriptor $project
     * @param Transformation    $transformation
     * @param string            $destination
     * @param \Twig_Environment $twigEnvironment
     *
     * @return void
     */
    protected function addPhpDocumentorExtension(
        ProjectDescriptor $project,
        Transformation $transformation,
        $destination,
        \Twig_Environment $twigEnvironment
    ) {
        $base_extension = new Extension($project, $transformation);
        $base_extension->setDestination(
            substr($destination, strlen($transformation->getTransformer()->getTarget()) + 1)
        );
        $base_extension->setRouters($this->routers);
        $base_extension->setTranslator($this->translator);
        $twigEnvironment->addExtension($base_extension);
    }

    /**
     * Tries to add any custom extensions that have been defined in the template or the transformation's configuration.
     *
     * This method will read the `twig-extension` parameter of the transformation (which inherits the template's
     * parameter set) and try to add those extensions to the environment.
     *
     * @param Transformation    $transformation
     * @param ProjectDescriptor $project
     * @param \Twig_Environment $twigEnvironment
     *
     * @throws \InvalidArgumentException if a twig-extension should be loaded but it could not be found.
     *
     * @return void
     */
    protected function addExtensionsFromTemplateConfiguration(
        Transformation $transformation,
        ProjectDescriptor $project,
        \Twig_Environment $twigEnvironment
    ) {
        /** @var \SimpleXMLElement $extension */
        foreach ((array) $transformation->getParameter('twig-extension', array()) as $extension) {
            $extension = (string) $extension;
            if (!class_exists($extension)) {
                throw new \InvalidArgumentException('Unknown twig extension: ' . $extension);
            }

            // to support 'normal' Twig extensions we check the interface to determine what instantiation to do.
            $implementsInterface = in_array(
                'phpDocumentor\Plugin\Core\Twig\ExtensionInterface',
                class_implements($extension)
            );

            $twigEnvironment->addExtension(
                $implementsInterface ? new $extension($project, $transformation) : new $extension()
            );
        }
    }

    /**
     * Uses the currently selected node and transformation to assemble the destination path for the file.
     *
     * The Twig writer accepts the use of a Query to be able to generate output for multiple objects using the same
     * template.
     *
     * The given node is the result of such a query, or if no query given the selected element, and the transformation
     * contains the destination file.
     *
     * Since it is important to be able to generate a unique name per element can the user provide a template variable
     * in the name of the file.
     * Such a template variable always resides between double braces and tries to take the node value of a given
     * query string.
     *
     * Example:
     *
     *   An artefact stating `classes/{{name}}.html` will try to find the
     *   node 'name' as a child of the given $node and use that value instead.
     *
     * @param DescriptorAbstract $node
     * @param Transformation     $transformation
     *
     * @throws \InvalidArgumentException if no artifact is provided and no routing rule matches.
     *
     * @return string|false returns the destination location or false if generation should be aborted.
     */
    protected function getDestinationPath($node, Transformation $transformation)
    {
        $writer = $this;

        if (!$node) {
            throw new \UnexpectedValueException(
                'The transformation node in the twig writer is not expected to be false or null'
            );
        }

        if (!$transformation->getArtifact()) {
            $rule = $this->routers->match($node);
            if (!$rule) {
                throw new \InvalidArgumentException(
                    'No matching routing rule could be found for the given node, please provide an artifact location, '
                    . 'encountered: ' . ($node === null ? 'NULL' : get_class($node))
                );
            }

            $rule = new ForFileProxy($rule);
            $url  = $rule->generate($node);
            if ($url === false || $url[0] !== '/') {
                return false;
            }
            $path = $transformation->getTransformer()->getTarget() . str_replace('/', DIRECTORY_SEPARATOR, $url);
        } else {
            $path = $transformation->getTransformer()->getTarget()
                . DIRECTORY_SEPARATOR . $transformation->getArtifact();
        }

        $destination = preg_replace_callback(
            '/{{([^}]+)}}/u',
            function ($query) use ($node, $writer) {
                // strip any surrounding \ or /
                return trim((string) $writer->walkObjectTree($node, $query[1]), '\\/');
            },
            $path
        );

        // replace any \ with the directory separator to be compatible with the
        // current filesystem and allow the next file_exists to do its work
        $destination = str_replace('\\', DIRECTORY_SEPARATOR, $destination);

        // create directory if it does not exist yet
        if (!file_exists(dirname($destination))) {
            mkdir(dirname($destination), 0777, true);
        }

        return $destination;
    }

    /**
     * Returns the path belonging to the template.
     *
     * @param Transformation $transformation
     *
     * @return string
     */
    protected function getTemplatePath($transformation)
    {
        $parts = preg_split('[\\\\|/]', $transformation->getSource());

        return $parts[0] . DIRECTORY_SEPARATOR . $parts[1];
    }

    /**
     * Sets the routers that can be used to determine the path of links.
     *
     * @param Queue $routers
     *
     * @return void
     */
    public function setRouters(Queue $routers)
    {
        $this->routers = $routers;
    }

    /**
     * @param \phpDocumentor\Translator $translator
     */
    public function setTranslator($translator)
    {
        $this->translator = $translator;
    }
}
