<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.4
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Renderer\Action;

use phpDocumentor\Descriptor\Analyzer;
use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\Interfaces\ProjectInterface;
use phpDocumentor\Plugin\Core\Transformer\Writer\Pathfinder;
use phpDocumentor\Plugin\Twig\Extension;
use phpDocumentor\Renderer\Action;
use phpDocumentor\Renderer\ActionHandler;
use phpDocumentor\Renderer\RenderPass;
use phpDocumentor\Transformer\Router\ForFileProxy;
use phpDocumentor\Transformer\Router\Queue;
use phpDocumentor\Transformer\Template;
use phpDocumentor\Transformer\Transformation;

class TwigHandler implements ActionHandler
{
    /** @var Analyzer */
    private $analyzer;

    /** @var Pathfinder */
    private $pathfinder;

    /** @var Queue */
    private $routers;

    /** @var array */
    private $templatesFolders = [];

    /** @var string */
    private $cacheFolder = '';

    public function __construct(
        Analyzer $analyzer,
        Pathfinder $pathfinder,
        Queue $routers,
        $templateFolders = [],
        $cacheFolder = null
    ) {
        if ($cacheFolder === null) {
            $cacheFolder = sys_get_temp_dir() . '/phpdoc-twig-cache';
        }

        $this->analyzer         = $analyzer;
        $this->pathfinder       = $pathfinder;
        $this->routers          = $routers;
        $this->templatesFolders = $templateFolders;
        $this->cacheFolder      = $cacheFolder;
    }

    /**
     * Executes the activities that this Action represents.
     *
     * @param Action|Twig $action
     *
     * @return void
     */
    public function __invoke(Action $action)
    {
        $actionDestination  = $action->getDestination();

        $nodes = $this->pathfinder->find($this->analyzer->getProjectDescriptor(), $action->getQuery());

        foreach ($nodes as $node) {
            if (!$node) {
                continue;
            }

            if (! $actionDestination) {
                $rule = $this->routers->match($node);
                if (!$rule) {
                    throw new \InvalidArgumentException(
                        'No matching routing rule could be found for the given node, please provide an artifact '
                        . 'location, encountered: ' . ($node === null ? 'NULL' : get_class($node))
                    );
                }

                $rule = new ForFileProxy($rule);
                $url  = $rule->generate($node);
                if ($url === false || $url[0] !== DIRECTORY_SEPARATOR) {
                    $destination = false;
                } else {
                    $destination = $actionDestination . $url;
                }
            } else {
                $destination = $this->getDestinationPath($node, $actionDestination);
            }

            if ($destination === false) {
                continue;
            }

            $destination = $action->getRenderPass()->getDestination() . '/' . ltrim($destination, '\\/');

            // create directory if it does not exist yet
            if (!file_exists(dirname($destination))) {
                mkdir(dirname($destination), 0777, true);
            }

            $environment = $this->initializeEnvironment($this->analyzer->getProjectDescriptor(), $destination, $action);
            $environment->addGlobal('node', $node);

            $html = $environment->render((string)$action->getView());
            file_put_contents($destination, $html);
        }
    }

    private function initializeEnvironment(ProjectInterface $project, $destination, Twig $action)
    {
        // move to local variable because we want to add to it without affecting other runs
        $templatesFolders = $this->templatesFolders;

        // Determine the path of the current template and prepend it to the list so that it will always be queried
        // first.
        // http://twig.sensiolabs.org/doc/recipes.html#overriding-a-template-that-also-extends-itself
        if ($action->getTemplate()) {
            $parameters = $action->getTemplate()->getParameters();
            if (isset($parameters['directory'])
                && file_exists($parameters['directory']->getValue())
                && is_dir($parameters['directory']->getValue())
            ) {
                array_unshift($templatesFolders, $parameters['directory']->getValue());
            } elseif ($action->getTemplate()->getName()) {
                foreach ($templatesFolders as $folder) {
                    $currentTemplatePath = $folder . '/' . $action->getTemplate()->getName();
                    if (file_exists($currentTemplatePath)) {
                        array_unshift($templatesFolders, $currentTemplatePath);
                        break;
                    }
                }
            }
        }

        $env = new \Twig_Environment(
            new \Twig_Loader_Filesystem($templatesFolders),
            array('cache' => $this->cacheFolder, 'auto_reload' => true)
        );

        $this->addPhpDocumentorExtension($project, $destination, $env, $action->getRenderPass());
//        $this->addExtensionsFromTemplateConfiguration($transformation, $project, $env);

        return $env;
    }

    /**
     * Adds the phpDocumentor base extension to the Twig Environment.
     *
     * @param ProjectInterface  $project
     * @param string            $destination
     * @param \Twig_Environment $twigEnvironment
     *
     * @return void
     */
    private function addPhpDocumentorExtension(
        ProjectInterface $project,
        $destination,
        \Twig_Environment $twigEnvironment,
        RenderPass $renderPass
    ) {
        $baseExtension = new Extension($project);
        $baseExtension->setDestination(
            substr($destination, strlen($renderPass->getDestination()) + 1)
        );
        $baseExtension->setRouters($this->routers);
        $twigEnvironment->addExtension($baseExtension);
    }

    /**
     * Tries to add any custom extensions that have been defined in the template or the transformation's configuration.
     *
     * This method will read the `twig-extension` parameter of the transformation (which inherits the template's
     * parameter set) and try to add those extensions to the environment.
     *
     * @param Transformation    $transformation
     * @param ProjectInterface  $project
     * @param \Twig_Environment $twigEnvironment
     *
     * @throws \InvalidArgumentException if a twig-extension should be loaded but it could not be found.
     *
     * @return void
     */
//    protected function addExtensionsFromTemplateConfiguration(
//        Transformation $transformation,
//        ProjectInterface $project,
//        \Twig_Environment $twigEnvironment
//    ) {
//        $isDebug = $transformation->getParameter('twig-debug')
//            ? $transformation->getParameter('twig-debug')->getValue()
//            : false;
//        if ($isDebug == 'true') {
//            $twigEnvironment->enableDebug();
//            $twigEnvironment->enableAutoReload();
//            $twigEnvironment->addExtension(new \Twig_Extension_Debug());
//        }
//
//        /** @var Template\Parameter $extension */
//        foreach ($transformation->getParametersWithKey('twig-extension') as $extension) {
//            $extensionValue = $extension->getValue();
//            if (!class_exists($extensionValue)) {
//                throw new \InvalidArgumentException('Unknown twig extension: ' . $extensionValue);
//            }
//
//            // to support 'normal' Twig extensions we check the interface to determine what instantiation to do.
//            $implementsInterface = in_array(
//                'phpDocumentor\Plugin\Twig\ExtensionInterface',
//                class_implements($extensionValue)
//            );
//
//            $twigEnvironment->addExtension(
//                $implementsInterface ? new $extensionValue($project, $transformation) : new $extensionValue()
//            );
//        }
//    }

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
     *   An artifact stating `classes/{{name}}.html` will try to find the
     *   node 'name' as a child of the given $node and use that value instead.
     *
     * @param DescriptorAbstract $node
     *
     * @throws \InvalidArgumentException if no artifact is provided and no routing rule matches.
     * @throws \UnexpectedValueException if the provided node does not contain anything.
     *
     * @return string|false returns the destination location or false if generation should be aborted.
     */
    private function getDestinationPath($node, $destination)
    {
        $destination = preg_replace_callback(
            '/{{([^}]+)}}/', // explicitly do not use the unicode modifier; this breaks windows
            function ($query) use ($node) {
                // strip any surrounding \ or /
                $filepart = trim((string)current($this->pathfinder->find($node, $query[1])), '\\/');
                $filepart = implode('/', array_map('urlencode', explode('/', $filepart)));

                return $filepart;
            },
            $destination
        );

        return $destination;
    }
}
