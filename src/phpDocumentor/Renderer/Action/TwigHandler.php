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
use phpDocumentor\Renderer\Action\Twig\Extension;
use phpDocumentor\Renderer\Action;
use phpDocumentor\Renderer\Action\Twig\Pathfinder;
use phpDocumentor\Renderer\ActionHandler;
use phpDocumentor\Renderer\RenderPass;
use phpDocumentor\Renderer\Template\PathsRepository;
use phpDocumentor\Transformer\Router\ForFileProxy;
use phpDocumentor\Transformer\Router\Queue;
use phpDocumentor\Views\ViewFactory;
use phpDocumentor\Views\Views;

class TwigHandler implements ActionHandler
{
    /** @var Analyzer */
    private $analyzer;

    /** @var Pathfinder */
    private $pathfinder;

    /** @var Queue */
    private $routers;

    /** @var PathsRepository */
    private $fileRepository;

    /** @var string */
    private $cacheFolder = '';

    /** @var ViewFactory */
    private $viewFactory;

    public function __construct(
        Analyzer $analyzer,
        Pathfinder $pathfinder,
        Queue $routers,
        PathsRepository $fileRepository,
        ViewFactory $viewFactory,
        $cacheFolder = null
    ) {
        if ($cacheFolder === null) {
            $cacheFolder = sys_get_temp_dir() . '/phpdoc-twig-cache';
        }

        $this->analyzer         = $analyzer;
        $this->pathfinder       = $pathfinder;
        $this->routers          = $routers;
        $this->fileRepository   = $fileRepository;
        $this->cacheFolder      = $cacheFolder;
        $this->viewFactory      = $viewFactory;
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
        $dataView = $this->viewFactory->create($action->getDataView(), $this->analyzer->getProjectDescriptor());
        $views    = new Views([$dataView->getName() => $dataView()]);

        // TODO: Move path finding to View
        $nodes = $this->pathfinder->find($dataView(), $action->getQuery());

        foreach ($nodes as $node) {
            if (!$node) {
                continue;
            }

            if (! ($action->getDestination())) {
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
                    $destination = $action->getDestination() . $url;
                }
            } else {
                $destination = $this->getDestinationPath($node, $action->getDestination());
            }

            if ($destination === false) {
                continue;
            }

            $destination = $action->getRenderPass()->getDestination() . '/' . ltrim($destination, '\\/');

            // create directory if it does not exist yet
            if (!file_exists(dirname($destination))) {
                mkdir(dirname($destination), 0777, true);
            }

            // move to local variable because we want to add to it without affecting other runs
            $templatesFolders = $this->fileRepository->listLocations($action->getTemplate());

            $environment = new \Twig_Environment(
                new \Twig_Loader_Filesystem($templatesFolders),
                array('cache' => $this->cacheFolder, 'auto_reload' => true)
            );

            $this->addPhpDocumentorExtension($views, $destination, $environment, $action->getRenderPass());
            // $this->addExtensionsFromTemplateConfiguration($transformation, $project, $environment);
            $environment->addGlobal('node', $node);

            $html = $environment->render((string)$action->getView());
            file_put_contents($destination, $html);
        }
    }

    /**
     * Adds the phpDocumentor base extension to the Twig Environment.
     *
     * @param Views             $views
     * @param string            $destination
     * @param \Twig_Environment $twigEnvironment
     *
     * @return void
     */
    private function addPhpDocumentorExtension(
        Views $views,
        $destination,
        \Twig_Environment $twigEnvironment,
        RenderPass $renderPass
    ) {
        $baseExtension = new Extension($views);
        $baseExtension->setDestination(substr($destination, strlen($renderPass->getDestination()) + 1));
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
